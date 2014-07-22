/**
 * @author: Alexander Zelensky
 * Gallery functionality JS file
 */

    $(document).ready(function(){

    	Dropzone.autoDiscover = false;

        /*************************************************************************/

		var dz_selector = ".egg-dropzone";
		
        $(dz_selector).each(function(index, el) {
            //console.log( index + ": " + $( this ).text() );

            var el_name = $(el).data("name");
			var gallery_id = $(el).data('gallery_id');

			var myDropzone = new Dropzone(
				el, {
                    url: "/admin/galleries/abstractupload",
                    addRemoveLinks : true,
                    maxFilesize: 5.0,
                    dictResponseError: 'Error uploading file!'
				}
			);

	        myDropzone.on("totaluploadprogress", function(data) {
	            //console.log(data);
	        });

			myDropzone.on("success", function(file, response) {
				//alert(response.image_id);
				$(el).append("<input type='hidden' name='" + el_name + "[uploaded_images][]' value='" + response.image_id + "' id='uploaded_image_" + response.image_id + "' />");
			});

			myDropzone.on("sending", function(file, xhr, formData) {
				//formData.append("filesize", file.size); // Will send the filesize along with the file as POST data.
				//console.log(file);
				//console.log(xhr);
				//console.log(formData);
				formData.append("gallery_id", gallery_id);
			});

			myDropzone.on("removedfile", function(file) {
				//console.log(file);
				// Как-то так...
                if (typeof file.xhr != 'undefined' && typeof file.xhr.response != 'undefined') {
    				var image_id = JSON.parse(file.xhr.response).image_id;
    				deleteUploadedImage(image_id);
                }
				//return false;
			});
            
        }); // jQuery.each

        /*************************************************************************/

		var dz_selector = ".egg-dropzone-single";
		
        $(dz_selector).each(function(index, el) {

            var el_name = $(el).data("name");
            var gallery_id = 0; //$(el).data('gallery_id');
			var preview = $(el).parent().find(".photo-preview");

			var myDropzone = new Dropzone(
				el, {
                    url: "/admin/galleries/singleupload",
                    addRemoveLinks : true,
                    maxFilesize: 5.0,
                    dictResponseError: 'Error uploading file!',
                    dictDefaultMessage: 'dictDefaultMessage',
                    uploadMultiple: false,
                    parallelUploads: 1,
                    maxFiles: 1,
                    dictMaxFilesExceeded: 'Можно загрузить только одно изображение.',
                    init: function() {
                        this.on("addedfile", function() {
                            // Single image upload
                            if (this.files[1] != null){
                                this.removeFile(this.files[0]);
                            }
                        });
                    }
				}
			);

	        myDropzone.on("totaluploadprogress", function(data) {
	            //console.log(data);
	        });

			myDropzone.on("success", function(file, response) {
                //console.log(file);
                //console.log(response);
				$(el).append("<input type='hidden' name='" + el_name + "' value='" + response.image_id + "' class='uploaded_image_" + response.image_id + "' />");
                $(el).parent().parent().find(".uploaded_image_false").empty().remove();
				$(el).hide();
                //$(el).find(".dz-preview").empty().remove();
                $(el).find(".dz-preview").hide();
                $(preview).css("background-image", "url("+response['thumb']+")");
                $(preview).find(".photo-full-link").attr("href", response['full']);
                $(preview).find(".photo-delete-single").attr("data-photo-id", response['image_id']);
                $(preview).show();
                $(preview).parents().find('.photo-preview-container').show();
			});

			myDropzone.on("sending", function(file, xhr, formData) {
				//formData.append("filesize", file.size); // Will send the filesize along with the file as POST data.
				//console.log(file);
				//console.log(xhr);
				//console.log(formData);
				formData.append("gallery_id", gallery_id);
			});

			myDropzone.on("removedfile", function(file) {
				//console.log(file);
				// Как-то так...
                if (typeof file.xhr != 'undefined' && typeof file.xhr.response != 'undefined') {
    				var image_id = JSON.parse(file.xhr.response).image_id;
    				deleteUploadedImage(image_id);
                }
				//return false;
			});
            
        }); // jQuery.each
        
        /*************************************************************************/
        
        $('.photo-delete').click(function(event){
            event.preventDefault();
            var image_id = $(this).attr('data-photo-id');
            var $photoDiv = $(this).parent();
    		$.SmartMessageBox({
    			title : "Удалить изображение?",
    			content : "",
    			buttons : '[Нет][Да]'
    		},function(ButtonPressed) {
    			if(ButtonPressed == "Да") {
                    $.ajax({
                        url: "/admin/galleries/photodelete",
                        data: { id: image_id },
                        type: 'post',
                    }).done(function(){
                        $photoDiv.fadeOut('fast');
                    }).fail(function(data){
                        $photoDiv.fadeOut('fast');
                        console.log(data);
                    });           
                    return false;
    			}
    		});
    		return false;
        });

        $('.photo-delete-single').click(function(event){
            event.preventDefault();
            var image_id = $(this).attr('data-photo-id');
            var $photoDiv = $(this).parent();
            var el = $(this).parents().find('.egg-dropzone-single');
            var preview = $(this).parents('.photo-preview');
    		$.SmartMessageBox({
    			title : "Удалить изображение?",
    			content : "",
    			buttons : '[Нет][Да]'
    		},function(ButtonPressed) {
    			if(ButtonPressed == "Да") {
                    $.ajax({
                        url: "/admin/galleries/photodelete",
                        data: { id: image_id },
                        type: 'post',
                    }).done(function(){
                        $photoDiv.fadeOut('fast');
                        //console.log(el);
                        $(".uploaded_image_" + image_id).empty().remove();
                        $(el).removeClass('dz-started');
                        $(el).show();
                        $(preview).hide();
                        $(preview).parents().find('.photo-preview-container').hide();
                    }).fail(function(data){
                        $photoDiv.fadeOut('fast');
                        console.log(data);
                    });           
                    return false;
    			}
    		});
    		return false;
        });

		function deleteUploadedImage(image_id) {
            $.ajax({
                url: "/admin/galleries/photodelete",
                data: { id: image_id },
                type: 'post',
            }).done(function(){
                $(".uploaded_image_" + image_id).empty().remove();
                //$photoDiv.fadeOut('fast');
            }).fail(function(data){
                console.log(data);
            });
            return false;
		}

    	//$('.superbox').SuperBox();

	});
