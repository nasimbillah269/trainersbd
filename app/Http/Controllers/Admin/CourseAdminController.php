<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseLesson;
use App\Models\CourseModule;
use App\Models\CourseReview;
use App\Models\Instructor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class CourseAdminController extends Controller
{
    /* =========================================================
       Courses
       ========================================================= */

    public function index(Request $r)
    {
        return $this->listCourses($r, false);
    }

    /** Separate "Free Courses" section (YouTube courses). */
    public function freeIndex(Request $r)
    {
        return $this->listCourses($r, true);
    }

    protected function listCourses(Request $r, bool $freeSection)
    {
        $type = $freeSection ? 'free' : (in_array($r->type, ['paid', 'free']) ? $r->type : null);
        $base = fn () => Course::query()->when($freeSection, fn ($q) => $q->where('type', 'free'));

        $courses = $base()->with(['category:id,name', 'instructor:id,name'])
            ->withCount(['lessons', 'orderItems as sales_count', 'approvedReviews as reviews_count'])
            ->withAvg('approvedReviews as rating_avg', 'rating')
            ->when($r->q, fn ($q) => $q->where('title', 'like', '%' . $r->q . '%'))
            ->when($r->category, fn ($q) => $q->where('category_id', $r->category))
            ->when($type, fn ($q) => $q->where('type', $type))
            ->when(in_array($r->status, ['active', 'draft']), fn ($q) => $q->where('status', $r->status))
            ->orderBy('sort')->latest('id')
            ->paginate(20)->withQueryString();

        $counts = [
            'all'      => $base()->count(),
            'active'   => $base()->where('status', 'active')->count(),
            'draft'    => $base()->where('status', 'draft')->count(),
            'free'     => Course::where('type', 'free')->count(),
            'paid'     => Course::where('type', 'paid')->count(),
            'lessons'  => $freeSection ? \App\Models\CourseLesson::whereHas('module.course', fn ($q) => $q->where('type', 'free'))->count() : null,
            'students' => $freeSection ? Course::where('type', 'free')->sum('students_count') : null,
        ];

        return view('admin.courses.index', [
            'courses'     => $courses,
            'counts'      => $counts,
            'categories'  => CourseCategory::orderBy('name')->get(['id', 'name']),
            'freeSection' => $freeSection,
        ]);
    }

    public function create(Request $r)
    {
        $course = new Course(['type' => $r->type === 'free' ? 'free' : 'paid', 'status' => 'active', 'language' => 'Bangla']);
        return $this->form($course);
    }

    public function edit(Course $course)
    {
        $course->load('modules.lessons');
        return $this->form($course);
    }

    protected function form(Course $course)
    {
        return view('admin.courses.edit', [
            'course'      => $course,
            'categories'  => CourseCategory::orderBy('sort')->orderBy('name')->get(['id', 'name']),
            'instructors' => Instructor::orderBy('name')->get(['id', 'name', 'title']),
        ]);
    }

    public function store(Request $r)
    {
        return $this->save($r, new Course());
    }

    public function update(Request $r, Course $course)
    {
        return $this->save($r, $course);
    }

    protected function save(Request $r, Course $course)
    {
        $data = $r->validate([
            'title'            => 'required|string|max:220',
            'slug'             => 'nullable|string|max:250|alpha_dash|unique:courses,slug,' . ($course->id ?: 'NULL'),
            'subtitle'         => 'nullable|string|max:500',
            'description'      => 'nullable|string',
            'category_id'      => 'nullable|exists:course_categories,id',
            'instructor_id'    => 'nullable|exists:instructors,id',
            'type'             => 'required|in:paid,free',
            'price'            => 'required_if:type,paid|nullable|numeric|min:0|max:9999999',
            'old_price'        => 'nullable|numeric|min:0|max:9999999',
            'level'            => 'nullable|string|max:80',
            'language'         => 'nullable|string|max:50',
            'duration'         => 'nullable|string|max:50',
            'badge'            => 'nullable|string|max:40',
            'students_count'   => 'nullable|integer|min:0',
            'preview_video'    => 'nullable|string|max:200',
            'thumbnail'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'thumbnail_alt'    => 'nullable|string|max:200',
            'learn_points'     => 'nullable|string|max:5000',
            'requirements'     => 'nullable|string|max:5000',
            'status'           => 'required|in:active,draft',
            'meta_title'       => 'nullable|string|max:200',
            'meta_description' => 'nullable|string|max:300',
            'meta_keywords'    => 'nullable|string|max:300',
            'sort'             => 'nullable|integer',
            'curriculum'       => 'nullable|json',
        ]);

        if ($data['preview_video'] ?? null) {
            if (!CourseLesson::youtubeId($data['preview_video'])) {
                return back()->withInput()->withErrors(['preview_video' => 'Please enter a valid YouTube link.']);
            }
        }

        $lines = fn ($text) => array_values(array_filter(array_map('trim', preg_split('/\r?\n/', (string) $text))));

        $course->fill([
            'title'            => $data['title'],
            'slug'             => ($data['slug'] ?? null) ?: Course::uniqueSlug($data['title'], $course->id),
            'subtitle'         => $data['subtitle'] ?? null,
            'description'      => clean_html($data['description'] ?? ''),
            'category_id'      => $data['category_id'] ?? null,
            'instructor_id'    => $data['instructor_id'] ?? null,
            'type'             => $data['type'],
            'price'            => $data['type'] === 'free' ? 0 : ($data['price'] ?? 0),
            'old_price'        => $data['type'] === 'free' ? 0 : ($data['old_price'] ?? 0),
            'level'            => $data['level'] ?? null,
            'language'         => ($data['language'] ?? null) ?: 'Bangla',
            'duration'         => $data['duration'] ?? null,
            'badge'            => $data['badge'] ?? null,
            'students_count'   => $data['students_count'] ?? 0,
            'preview_video'    => $data['preview_video'] ?? null,
            'thumbnail_alt'    => $data['thumbnail_alt'] ?? null,
            'learn_points'     => $lines($data['learn_points'] ?? ''),
            'requirements'     => $lines($data['requirements'] ?? ''),
            'featured'         => $r->boolean('featured'),
            'status'           => $data['status'],
            'meta_title'       => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
            'meta_keywords'    => $data['meta_keywords'] ?? null,
            'sort'             => $data['sort'] ?? 0,
        ]);

        if ($r->hasFile('thumbnail')) {
            $old = $course->thumbnail;
            $course->thumbnail = $this->storeImage($r->file('thumbnail'), 'courses', 1200, 750);
            if ($old && Str::startsWith($old, 'medies/courses/') && $old !== $course->thumbnail) {
                File::delete(public_path($old));
            }
        } elseif ($r->boolean('remove_thumbnail') && $course->thumbnail) {
            File::delete(public_path($course->thumbnail));
            $course->thumbnail = null;
        }

        DB::transaction(function () use ($course, $data) {
            $course->save();
            if (isset($data['curriculum'])) {
                $this->syncCurriculum($course, json_decode($data['curriculum'], true) ?: []);
            }
        });

        return redirect()->route('admin.courses.edit', $course)->with('success', 'Course saved successfully');
    }

    /** Replace the module / lesson tree with the editor payload. */
    protected function syncCurriculum(Course $course, array $modules): void
    {
        $keepModules = [];
        $keepLessons = [];

        foreach (array_values($modules) as $mi => $m) {
            $title = trim((string) ($m['title'] ?? ''));
            if ($title === '') {
                continue;
            }
            $module = (!empty($m['id']) ? $course->modules()->find($m['id']) : null) ?: new CourseModule(['course_id' => $course->id]);
            $module->fill(['title' => Str::limit($title, 200, ''), 'sort' => $mi])->save();
            $keepModules[] = $module->id;

            foreach (array_values($m['lessons'] ?? []) as $li => $l) {
                $lt = trim((string) ($l['title'] ?? ''));
                if ($lt === '') {
                    continue;
                }
                $lesson = (!empty($l['id']) ? CourseLesson::where('course_id', $course->id)->find($l['id']) : null) ?: new CourseLesson(['course_id' => $course->id]);
                $lesson->fill([
                    'module_id'  => $module->id,
                    'title'      => Str::limit($lt, 220, ''),
                    'video'      => Str::limit(trim((string) ($l['video'] ?? '')), 200, '') ?: null,
                    'duration'   => Str::limit(trim((string) ($l['duration'] ?? '')), 20, '') ?: null,
                    'is_preview' => !empty($l['is_preview']),
                    'sort'       => $li,
                ])->save();
                $keepLessons[] = $lesson->id;
            }
        }

        CourseLesson::where('course_id', $course->id)->whereNotIn('id', $keepLessons ?: [0])->delete();
        CourseModule::where('course_id', $course->id)->whereNotIn('id', $keepModules ?: [0])->delete();
    }

    public function destroy(Course $course)
    {
        if ($course->thumbnail && Str::startsWith($course->thumbnail, 'medies/courses/')) {
            File::delete(public_path($course->thumbnail));
        }
        $course->delete();
        return redirect()->route('admin.courses.index')->with('success', 'Course deleted');
    }

    public function duplicate(Course $course)
    {
        $course->load('modules.lessons');
        $copy = $course->replicate(['students_count']);
        $copy->title = $course->title . ' (Copy)';
        $copy->slug = Course::uniqueSlug($copy->title);
        $copy->status = 'draft';
        $copy->save();
        foreach ($course->modules as $m) {
            $nm = $copy->modules()->create(['title' => $m->title, 'sort' => $m->sort]);
            foreach ($m->lessons as $l) {
                $nm->lessons()->create($l->only(['title', 'video', 'duration', 'is_preview', 'sort']) + ['course_id' => $copy->id]);
            }
        }
        return redirect()->route('admin.courses.edit', $copy)->with('success', 'Course duplicated as a draft');
    }

    public function quickUpdate(Request $r, Course $course)
    {
        $r->validate(['field' => 'required|in:status,featured', 'value' => 'required']);
        if ($r->field === 'status') {
            $course->status = $r->value === 'active' ? 'active' : 'draft';
        } else {
            $course->featured = filter_var($r->value, FILTER_VALIDATE_BOOLEAN);
        }
        $course->save();
        return response()->json(['success' => true]);
    }

    protected function storeImage($file, string $folder, int $w, int $h): string
    {
        $dir = 'medies/' . $folder . '/' . now()->format('Y-m');
        File::ensureDirectoryExists(public_path($dir));
        $name = Str::limit(Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'image', 60, '') . '-' . Str::random(6);

        // Crop + compress when an image library is available; otherwise keep the original file.
        if (extension_loaded('gd') || extension_loaded('imagick')) {
            $path = $dir . '/' . $name . '.jpg';
            Image::make($file)->fit($w, $h)->encode('jpg', 82)->save(public_path($path));
            return $path;
        }

        $ext = strtolower($file->guessExtension() ?: $file->getClientOriginalExtension() ?: 'jpg');
        $file->move(public_path($dir), $name . '.' . $ext);
        return $dir . '/' . $name . '.' . $ext;
    }

    /* =========================================================
       Categories
       ========================================================= */

    public function categories()
    {
        return view('admin.courses.categories', [
            'categories' => CourseCategory::withCount('courses')->orderBy('sort')->orderBy('name')->get(),
        ]);
    }

    public function categorySave(Request $r, ?CourseCategory $category = null)
    {
        $category = $category && $category->exists ? $category : new CourseCategory();
        $data = $r->validate([
            'name'        => 'required|string|max:150',
            'slug'        => 'nullable|alpha_dash|max:180|unique:course_categories,slug,' . ($category->id ?: 'NULL'),
            'icon'        => ['nullable', 'regex:/^bi-[a-z0-9-]+$/'],
            'description' => 'nullable|string|max:1000',
            'sort'        => 'nullable|integer',
        ], ['icon.regex' => 'Use a Bootstrap Icons class, e.g. bi-code-slash']);

        $category->fill($data);
        $category->slug = ($data['slug'] ?? null) ?: Str::slug($data['name']);
        $category->sort = $data['sort'] ?? 0;
        $category->status = $r->boolean('status', true);
        $category->save();

        return back()->with('success', 'Category saved');
    }

    public function categoryDelete(CourseCategory $category)
    {
        $category->delete();
        return back()->with('success', 'Category deleted. Its courses are now uncategorised.');
    }

    /* =========================================================
       Instructors
       ========================================================= */

    public function instructors()
    {
        return view('admin.courses.instructors', [
            'instructors' => Instructor::withCount('courses')->orderBy('name')->get(),
        ]);
    }

    public function instructorSave(Request $r, ?Instructor $instructor = null)
    {
        $instructor = $instructor && $instructor->exists ? $instructor : new Instructor();
        $data = $r->validate([
            'name'  => 'required|string|max:150',
            'title' => 'nullable|string|max:150',
            'bio'   => 'nullable|string|max:3000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);
        $instructor->fill(collect($data)->except('photo')->all());
        if ($r->hasFile('photo')) {
            if ($instructor->photo) {
                File::delete(public_path($instructor->photo));
            }
            $instructor->photo = $this->storeImage($r->file('photo'), 'instructors', 400, 400);
        }
        $instructor->status = $r->boolean('status', true);
        $instructor->save();
        return back()->with('success', 'Instructor saved');
    }

    public function instructorDelete(Instructor $instructor)
    {
        if ($instructor->photo) {
            File::delete(public_path($instructor->photo));
        }
        $instructor->delete();
        return back()->with('success', 'Instructor deleted');
    }

    /* =========================================================
       Reviews
       ========================================================= */

    public function reviews(Request $r)
    {
        $status = in_array($r->status, ['pending', 'approved', 'rejected']) ? $r->status : null;
        return view('admin.courses.reviews', [
            'reviews' => CourseReview::with('course:id,title,slug')
                ->when($status, fn ($q) => $q->where('status', $status))
                ->when($r->q, fn ($q) => $q->where(fn ($qq) => $qq->where('name', 'like', "%{$r->q}%")->orWhere('review', 'like', "%{$r->q}%")))
                ->latest()->paginate(20)->withQueryString(),
            'counts' => CourseReview::selectRaw('status, count(*) c')->groupBy('status')->pluck('c', 'status'),
            'status' => $status,
        ]);
    }

    public function reviewStatus(Request $r, CourseReview $review)
    {
        $r->validate(['status' => 'required|in:pending,approved,rejected']);
        $review->update(['status' => $r->status]);
        return back()->with('success', 'Review ' . $r->status);
    }

    public function reviewDelete(CourseReview $review)
    {
        $review->delete();
        return back()->with('success', 'Review deleted');
    }
}
