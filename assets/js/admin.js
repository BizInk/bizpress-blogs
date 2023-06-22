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
            $(this).parent().parent().addClass('post_in_library');
            $('#bizpress_blogs_addpost_model .article_title').text($(this).data('title'));
            $("#bizpress_blogs_addpost_model").addClass('show');
            $('#bizpress_blogs_addpost_model .model_close').hide();
            $.ajax({
                type: "post",
                dataType: "json",
                url: bizpress_blogs_ajax_object.ajaxurl,
                data: {
                    bizpressPostID: $(this).data('id'),
                    action: 'bizpressblogsarticle'
                },
                success: function(response){
                    $('.article_status').text(response.message);
                    if(response.status == 'success'){
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
                    $('.article_status').text("Sorry there was and error when adding the post");
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

    $("#bizink_blogs_loader").hide();
    $('#main_loader_section').hide();

    function getBlogs(){
        let category = $('#bizpress_blogs_category').val();
        let search = $('#bizpress_blogs_search').val();
        let page = $('.bizpress_blogs_posts').data('page');
        let totalpages = $('.bizpress_blogs_posts').data('totalpages');
        $('.pagenation_page_button').each(function(){
            $(this).removeClass('selected');
            if($(this).data('page') == page){
                $(this).addClass('selected');
            }

        });
        $('.prev_button').prop('disabled', false);
        $('.next_button').prop('disabled', false);
        if(page == 1){
            $('.prev_button').prop('disabled', true);
        }
        if(page == totalpages){
            $('.next_button').prop('disabled', true);
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
                ...(page > 1 && {blogpage:page})
            },
            success: function(response){
                $("#bizink_blogs_loader").hide();
                $('#main_loader_section').hide();
                $('.bizpress_blogs_posts .bizpress_blog_items').show();
                if(response.status == 'success'){
                    // Paganation
                    $('.pagenation_page_button').each(function(){
                        $(this).remove();
                    });

                    $('.bizpress_blogs_posts').data('page',response.page);
                    $('.bizpress_blogs_posts').data('totalpages',response.totalpages);
                    if(response.totalpages > 10){
                        let i = 0;
                        while(i < response.totalpages){
                            let selected = '';
                            if(i == response.page - 1){ selected = 'selected'; }
                            $('.pagenation_pages').append('<button class="pagenation_page_button '+selected+'" data-page="'+(i+1)+'">'+(i+1)+'</button>');
                            i++;
                        }
                    }
                    else{
                        let has_echo_elipics = false;
                        let i = 0;
                        while(i < response.totalpages){
                            let selected = '';
                            if(i == response.page - 1){ selected = 'selected'; }

                            if(i < 4 || i > response.totalpages - 2){
                                $('.pagenation_pages').append('<button class="pagenation_page_button '+selected+'" data-page="'+(i+1)+'">'+(i+1)+'</button>');
                            }
                            else{
                                has_echo_elipics = true;
                                $('.pagenation_pages').append('<div class="pagenation_elipics_button"><span class="pagenation_button_text">...</span></div>');
                            }
                            i++;
                        }
                    }

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
                    if(category != null && category != '' || search != null || search != "" || page > 1){
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
        getBlogs();
    });
    $('#bizpress_blogs_category').on('change',function(){
        getBlogs();
    });
    $('.pagenation_page_button').click(function(){
        setPage($(this).data('page'));
        getBlogs();
    });
    $('.prev_button').click(function(){
        let page = $('.bizpress_blogs_posts').data('page') - 1;
        if(page > 0){
            setPage(page);
            getBlogs();
        }
        else{
            $('.prev_button').prop('disabled', true);
        }
    });
    $('.next_button').click(function(){
        let totalpages = $('.bizpress_blogs_posts').data('totalpages');
        let page = $('.bizpress_blogs_posts').data('page') + 1;
        if(page < (totalpages + 1)){
            setPage(page);
            getBlogs();
        }
        else{
            $('.next_button').prop('disabled', true);
        }
    });
});