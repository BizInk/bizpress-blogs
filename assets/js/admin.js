/* Bizpress Blogs */
const { __, _x, _n, _nx } = wp.i18n;

function eventBindings(){
    (function($){
        $('#bizpress_blogs_category').on('focus',function(){
            $('#bizpress_blogs_category').parent().addClass('open');
        });
        $('#bizpress_blogs_category').on('blur',function(){
            $('#bizpress_blogs_category').parent().removeClass('open');
        });
        $('.close_model').click(function(){
            if($(this).is(":disabled") == false){
                $('#bizpress_blogs_model').removeClass('show');
                $("#bizpress_blogs_addpost_model").removeClass('show');
            }
        });
    
        $('.view_article').click(function(){
            $('#bizpress_blogs_model .import_model').data('id',$(this).parent().parent().data('id'));
            $('#bizpress_blogs_model .import_model').data('title',$(this).parent().parent().data('title'));
            $('#bizpress_blogs_model .model .model_title').text($(this).parent().parent().data('title'));
            $('#bizpress_blogs_model .model .model_content').html($(this).parent().parent().find('.content').html());
            $('#bizpress_blogs_model').addClass('show');
        });

        $('.view_post').click(function(){
            window.open(bizpress_blogs_ajax_object.posturl+'?post='+$(this).data('id')+'&action=edit','_blank');
        });

        $('.import_article').click(function(){
            let importedArticle = $(this);
            $('#bizpress_blogs_addpost_model .article_title').text(importedArticle.data('title'));
            $("#bizpress_blogs_addpost_model").addClass('show');
            $('#bizpress_blogs_addpost_model .model_close').hide();
            let publisher = $('#bizpress_blogs_publisher').val();
            if(publisher == null || publisher == ''){
                publisher = 'bizink';
            }
            $.ajax({
                type: "post",
                dataType: "json",
                url: bizpress_blogs_ajax_object.ajaxurl,
                data: {
                    bizpressPostID: $(this).data('id'),
                    action: 'bizpressblogsarticle',
                    publisher: publisher
                },
                success: function(response){                    
                    $('.article_status').text(response.message);
                    if(response.status == 'success'){
                        importedArticle.parent().parent().addClass('post_in_library');
                        $('#bizpress_blogs_addpost_model .view_model').prop('disabled', false);
                        $('#bizpress_blogs_addpost_model .close_model').prop('disabled', false);
                        $('#bizpress_blogs_addpost_model .model_close').show();
                        $('#bizpress_blogs_addpost_model .loader_section').hide();
                        $('#bizpress_blogs_addpost_model .view_post').data('id',response.post_id);
                        let prevPosts = $('.bizpress_blogs_posts').data('posts');
                        prevPosts.push(parseInt($(this).data('id')));
                        $('.bizpress_blogs_posts').data('posts',prevPosts);
                        $('.bizpress_blogs_posts').attr('data-posts',prevPosts);
                    }
                    else{
                        $('#bizpress_blogs_addpost_model .close_model').prop('disabled', false);
                        $('#bizpress_blogs_addpost_model .model_close').show();
                        $('#bizpress_blogs_addpost_model .loader_section').hide();
                    }
                },
                error: function(response){
                    console.log("Bizpress Blogs: " + response.responseJSON.message);
                    $('.article_status').text(response.responseJSON.message ? response.responseJSON.message : "Sorry there was and error when adding the post");
                    $('#bizpress_blogs_addpost_model .close_model').prop('disabled', false);
                    $('#bizpress_blogs_addpost_model .model_close').show();
                    $('#bizpress_blogs_addpost_model .loader_section').hide();
                }
            });
        });
    
        $('.model_content').on('mousedown',function(){return false;});
        $('.model_content').on('selectstart',function(){return false;});
        $('.blog .blog_excerpt').on('mousedown',function(){return false;});
        $('.blog .blog_excerpt').on('selectstart',function(){return false;});
    
        $(document).bind("contextmenu",function(e){
            return false;
        });
        $('body').bind('cut copy paste', function(event) {
            event.preventDefault();
        });
    })(jQuery); 
}

jQuery(document).ready(function($){

    // Background Header Image
    let headerImageNumber = Math.floor(Math.random() * 10) + 1;
    $('.bizpress_blogs .bizpress_blogs_header').removeClass([ 'bg1','bg2', 'bg3']);
    switch(headerImageNumber){
        case 1:
            $('.bizpress_blogs .bizpress_blogs_header').addClass('bg1');
            $('.bizpress_blogs .bizpress_blogs_header .photocredit a').text('Danny Postma');
            $('.bizpress_blogs .bizpress_blogs_header .photocredit a').attr('href','https://unsplash.com/@dannypostma?utm_source=unsplash&utm_medium=referral&utm_content=creditCopyText');
            break;
        case 3:
            $('.bizpress_blogs .bizpress_blogs_header').addClass('bg3');
            $('.bizpress_blogs .bizpress_blogs_header .photocredit a').text("Ken Cheung");
            $('.bizpress_blogs .bizpress_blogs_header .photocredit a').attr('href','https://unsplash.com/@kencheungphoto?utm_source=unsplash&utm_medium=referral&utm_content=creditCopyText');
            break;
        default:
            $('.bizpress_blogs .bizpress_blogs_header').addClass('bg2');
            $('.bizpress_blogs .bizpress_blogs_header .photocredit a').text("Graham Holtshausen");
            $('.bizpress_blogs .bizpress_blogs_header .photocredit a').attr('href','https://unsplash.com/@freedomstudios?utm_source=unsplash&utm_medium=referral&utm_content=creditCopyText');
            break;
    }
    eventBindings();
    bizpress_getBlogs();

    function bizpress_reload_categories(){
        $('#bizpress_blogs_category').prop( "disabled", true );
        let publisher = $('#bizpress_blogs_publisher').val();
        if(publisher == null || publisher == ''){
            publisher = 'bizink';
        }
        $.ajax({
            type: "post",
            dataType: "json",
            url: bizpress_blogs_ajax_object.ajaxurl,
            data: {
                action: 'bizpressblogscategories',
                ...(publisher && {publisher:publisher})
            },
            success: function(response){
                if(response.status == 'success'){
                    $('#bizpress_blogs_category').html('');
                    $('#bizpress_blogs_category').append('<option value="all">'+__('All Posts','bizink-client')+'</option>');
                    response.categories.forEach(category => {
                        $('#bizpress_blogs_category').append('<option value="'+category.id+'">'+category.name+'</option>');
                    });
                    $('#bizpress_blogs_category').prop( "disabled", false );
                }
                else{
                    console.log(response);
                }
            },
            error: function(response){
                console.log(response);
            }
        });
    }

    function bizpress_pagenation_button(page,selected){
        let selected_text = '';
        if(selected){
            selected_text = 'selected';
        }
        $('.pagenation_pages').append('<button type="button" data-page="'+page+'" class="pagenation_button pagenation_page_button '+selected_text+'">'+page+'</button>');
    }

    function bizpress_pagenation_section(){
        $('.pagenation_page_button').each(function(){
            $(this).remove();
        });

        let totalPages = parseInt($('.bizpress_blogs_posts').data('totalpages'));
        let current_page = parseInt($('.bizpress_blogs_posts').data('page'));
        
        $('.prev_button').prop('disabled', false);
        $('.next_button').prop('disabled', false);
        if(current_page == 1){
            $('.prev_button').prop('disabled', true);
        }
        if(current_page == totalPages){
            $('.next_button').prop('disabled', true);
        }

        if(totalPages < 2){
            $('.pagenation').hide();
        }
        else if(totalPages < 10){
            $('.pagenation').show();
            $('.pagenation_pages').html('');
            let i = 1;
            while(i < totalPages){
                let selected = false;
                if(i == current_page){ 
                    selected = true;
                }
                bizpress_pagenation_button(i,selected);
                i++;
            }
        }
        else{
            $('.pagenation').show();
            $('.pagenation_pages').html('');
            let has_echo_elipics = false;
            let i = 1;
            while(i < totalPages){
                let selected = false;
                if(i == current_page){ 
                    selected = true;
                }

                if(i == current_page || i == current_page - 1 || i == current_page + 1 || i == totalPages || i == totalPages - 1 || i == 1){
                    bizpress_pagenation_button(i,selected);
                }
                else if(current_page < totalPages - 3 && has_echo_elipics == false){
                    has_echo_elipics = true;
                    $('.pagenation_pages').append('<div type="button" class="pagenation_button pagenation_elipics_button"><span class="pagenation_button_text">...</span></div>');
                }
                i++;
            }
        }
        $('.pagenation_page_button').click(function(){
            if(setPage($(this).data('page'))){
                bizpress_getBlogs();
            }
        });
    }

    function bizpress_getBlogs(){
        $("#bizink_blogs_loader").show();
        $('#main_loader_section').show();
        $('.pagenation').hide();

        let category = $('#bizpress_blogs_category').val();
        let search = $('#bizpress_blogs_search').val();
        let page = $('.bizpress_blogs_posts').data('page');
        let publisher = $('#bizpress_blogs_publisher').val();
        if(publisher == null || publisher == ''){
            publisher = 'bizink';
        }

        $('#main_loader_section').height($('.bizpress_blogs_posts .bizpress_blog_items').height())
        $('#main_loader_section').show();
        $("#bizink_blogs_loader").show();
        $('.bizpress_blogs_posts .bizpress_blog_items').hide();

        $.ajax({
            type: "post",
            dataType: "json",
            url: bizpress_blogs_ajax_object.ajaxurl,
            data: {
                action: 'bizpressblogs',
                ...(category && {category}),
                ...(search && {search}),
                ...(page > 1 && {blogpage:page}),
                ...(publisher && {publisher:publisher})
            },
            success: function(response){
                $("#bizink_blogs_loader").hide();
                $('#main_loader_section').hide();
                $('.bizpress_blogs_posts .bizpress_blog_items').show();
                if(response.status == 'success'){
                    let totalPages = parseInt(response.totalPages);
                    $('.bizpress_blogs_posts').data('totalpages',totalPages);
                    let current_page = parseInt(page);
                    $('.bizpress_blogs_posts').data('page',current_page);

                    // Success but No Posts
                    if(response.posts == null || response.posts.length < 1){
                        $('.pagenation').hide();
                        $('.no_posts').show();
                        $('.bizpress_blogs_posts .bizpress_blog_items').hide();
                        return;
                    }

                    bizpress_pagenation_section();

                    // Blogs
                    $('.blog').each(function(){
                        $(this).remove();
                    });
                    let prevPosts = $('.bizpress_blogs_posts').data('posts');
                    response.posts.forEach(post => {
                        let inLibary = prevPosts.includes(post.id) ? 'post_in_library':'';
                        $('.bizpress_blog_items').append(
                        '<div class="blog '+inLibary+'" id="bizpress_blog_'+post.id+'" data-slug="'+post.slug+'" data-id="'+post.id+'" data-title="'+post.title.rendered+'"><div class="in_library_text">'+__('In Library','bizink-client')+'</div><div class="blog_text"><h3 class="blog_title">'+post.title.rendered+'</h3><div class="blog_excerpt" onmousedown="return false" onselectstart="return false">'+post.excerpt.rendered+'</div></div><div class="actions"><button type="button" class="bizpress_blogs_button view_article">'+__('View Article','bizink-client')+'</button><button type="button" class="bizpress_blogs_button bizpress_blogs_button_secondary import_article" data-id="'+post.id+'" data-title="'+post.title.rendered+'">'+__('Import Article','bizink-client')+'</button></div><div class="content" style="display: none;" onmousedown="return false" onselectstart="return false">'+post.content.rendered+'</div>');
                    });
                    eventBindings();
                    if(category != null && category != '' || search != null || search != "" || page > 1 || publisher != 'bizink'){
                        if (history.pushState) {
                            let newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?page=bizpress_blogs';
                            if(page > 1){
                                newurl = newurl + '&blogpage='+page;
                            }
                            if(category != null && category != '' && category != undefined && category != 'all'){
                                newurl = newurl + '&category='+category;
                            }
                            if(search != null && search != "" && search != undefined){
                                newurl = newurl + '&search='+search;
                            }
                            if(publisher != 'bizink'){
                                newurl = newurl + '&publisher='+publisher;
                            }
                            window.history.pushState({path:newurl},'',newurl);
                        }
                    }
                    
                }
                else{
                    $("#bizink_blogs_loader").hide();
                    $('#main_loader_section').hide();
                    $('.bizpress_blogs_posts .bizpress_blog_items').show();
                    console.log(response);
                    let message = response.message ? response.message : 'Sorry we are unable to retreve and blog posts at this time';
                    $('.bizpress_blogs_pagenation').before('<p class="bizpress_blog_status bizpress_blog_status_error">'+message+'</p>');
                    setTimeout(() => {
                        $('.bizpress_blog_status').hide();
                    }, 3000);
                }
            },
            error: function(response){
                console.log(response);
                let message = response.message ? response.message : 'Sorry we are unable to retreve and blog posts at this time';
                $('.bizpress_blogs_pagenation').before('<p class="bizpress_blog_status bizpress_blog_status_error">'+message+'</p>');
                $("#bizink_blogs_loader").hide();
                $('#main_loader_section').hide();
                $('.bizpress_blogs_posts .bizpress_blog_items').show();
                setTimeout(() => {
                    $('.bizpress_blog_status').hide();
                }, 3000);
            }
        });

    }

    function setPage(page){
        let totalpages = $('.bizpress_blogs_posts').data('totalpages');
        if(page > totalpages || page < 1){
            return false;
        }
        $('.bizpress_blogs_posts').data('page',page);
        return true;
    }

    function setCategory(category){
        if(category == null || category == ''){
            return false;
        }
        $('#bizpress_blogs_category').val(category);
        return true;
    }

    $('#bizpress_blogs_search_form_submit').click(function(){
        bizpress_getBlogs();
    });
    $('#bizpress_blogs_category').on('change',function(){
        bizpress_getBlogs();
    });
    $('#bizpress_blogs_publisher').on('change',function(){
        $('.bizpress_blogs_posts').data('page',1);
        bizpress_reload_categories();
        bizpress_getBlogs();
    });
    
    $('.prev_button').click(function(){
        if(setPage(($('.bizpress_blogs_posts').data('page') - 1))){
            bizpress_getBlogs();
        }
        else{
            $('.prev_button').prop('disabled', true);
        }
    });
    $('.next_button').click(function(){
        if(setPage(($('.bizpress_blogs_posts').data('page') + 1))){
            bizpress_getBlogs();
        }
        else{
            $('.next_button').prop('disabled', true);
        }
    });
});