     <script type="text/javascript">
         
      $( function() {
              $( "#sortable" ).sortable();
              $( "#sortable" ).disableSelection();
              $( ".sortable" ).sortable();
              $( ".sortable" ).disableSelection();
          } );
    </script>
     <script>
      $(document).ready(function(){
          
         

          
          tinymce.init({
            selector: 'textarea.tinyEditor',
            height: 300,
            menubar: false,
            statusbar: false,
            plugins: 'lists advlist image link fullscreen advcode code',
            toolbar: 'undo redo | styles | bold italic underline | alignleft aligncenter alignright alignjustify |' + 
            'bullist numlist outdent advlist | link image | preview media fullscreen  | code |' +
            'forecolor backcolor emoticons',
            image_title: true,
            automatic_uploads: true,
            file_picker_types: 'image',
            file_picker_callback: function (cb, value, meta) {
                var input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/*');
                input.onchange = function () {
                  var file = this.files[0];
                  var reader = new FileReader();
                  reader.onload = function () {
                    var id = 'blobid' + (new Date()).getTime();
                    var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
                    var base64 = reader.result.split(',')[1];
                    var blobInfo = blobCache.create(id, file, base64);
                    blobCache.add(blobInfo);
                    cb(blobInfo.blobUri(), { title: file.name });
                  };
                  reader.readAsDataURL(file);
                };
                input.click();
              },
            content_style: 'body{font-family:Helvetica,Arial,sans-serif; font-size:16px}'
        });
        
        
        
        

        $('#PrintAction').on("click", function () {
            $('.PrintAreaContact').printThis();
          });

        $('#PrintAction2').on("click", function () {
            $('.PrintAreaContact2').printThis();
          });
          
          // When a file is selected, update the image preview
        $(document).on('change','.profile-upload', function (e) {
            var input = e.target;
            var profiImage ='.'+$(this).data('imageshow');
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $(profiImage).attr('src', e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
        });

         $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
            });

            $(document).on('click','.reloadPage',function(){

                location.reload();
                return true;

            });
          
          $(document).on('click','.showPassword',function(){
                $(this).toggleClass('active-show');
                if ($(this).hasClass('active-show')) {
                    $('input.password').prop('type','text');
                    $(this).empty().append('<i class="fa fa-eye"></i>');
                } else {
                    $('input.password').prop('type','password');
                    $(this).empty().append('<i class="fa fa-eye-slash"></i>');
                }
            });
           
           $(document).on('change','.paymentMethod',function(){
                  var id = $(this).val();
                  if(id==''){
                   $('.paymentOption').empty().append();
                  }
                  var url ='{{url('payments/filter')}}' + '/'+id;
                  $.get(url,function(data){
                    $('.paymentOption').empty().append(data.paymentData);
                  });   
            });


          $("#division").on("change", function(){
                var id = $(this).val();
                  if(id==''){
                   $('#district').empty().append('<option value="">No District</option>');
                   $('#city').empty().append('<option value="">No City</option>');
                  }
                  var url ='{{url('geo/filter')}}' + '/'+id;
                  $.get(url,function(data){
                    $('#district').empty().append(data.geoData);
                    $('#city').empty().append('<option value="">No City</option>');
                  });   
            });

            $("#district").on("change", function(){
                var id = $(this).val();
                  if(id==''){
                   $('#city').empty().append('<option value="">No City</option>');
                  }
                  var url ='{{url('geo/filter')}}' + '/'+id;
                  $.get(url,function(data){
                    $('#city').empty().append(data.geoData);  
                  });   
            });


            $('.mediaDelete').click(function(e){
                e.preventDefault();

              var url =$(this).attr('href');

              if(confirm("Are you sure you want to delete this?")){
                
                $.ajax({
                  url : url,
                  type:'GET',
                  cache: false,
                  contentType: false,
                  dataType: 'json',
                  beforeSend: function()
                  {
                    
                  },
                  complete: function()
                  {
                      
                  },
                  }).done(function (data) {
                     
                     location.reload(true);
                    
                  }).fail(function () {
                      alert('fail');
                  });
                  
              }else{
                  return false;
              }

            });
          
      });
    </script>

    <script type="text/javascript">
      ///Check Box Select With Count show

          $(function() {
            $('.checkCounter').text('0');
            var generallen = $("input[name='checkid[]']:checked").length;
            if (generallen > 0) {
              $(".checkCounter").text('(' + generallen + ')');
            } else {
              $(".checkCounter").text(' ');
            }
            
          })
          
          function updateCounter() {
            var len = $("input[name='checkid[]']:checked").length;
            if (len > 0) {
                $('.deleteBtnStatus').show();
              $(".checkCounter").text('(' + len + ')');
            } else {
                $('.deleteBtnStatus').hide();
              $(".checkCounter").text(' ');
            }
          }
          
          $(document).on("change","input[name='checkid[]']", function() {
            updateCounter();
          });

       
 
            $(document).on('click','#checkall',function() {
              var checked = $(this).prop('checked');
              $("input[name='checkid[]']").prop('checked', checked);
              updateCounter();
            });

        
        ///Check Box Select With Count show
      </script>

    
