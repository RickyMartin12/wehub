<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/10.0.0/css/bootstrap-slider.min.css">
<link rel="stylesheet" href="https://www.cssscript.com/demo/animated-customizable-range-slider-pure-javascript-rslider-js/css/rSlider.min.css">
<script src="https://www.cssscript.com/demo/animated-customizable-range-slider-pure-javascript-rslider-js/js/rSlider.min.js"></script>

<style>
.box2 {
  width: 100%;    
  border-radius:10px;
  border: 1px solid #ccc;
  background: #fff;
  margin-bottom: 50px;
  
}
.box2:hover {
  box-shadow: 0 0 11px rgba(33,33,33,.2); 
}
.card-title > a {
    color: #000;
}
.card-title > a:hover {
    opacity: 0.8;
}
.post-content {
    padding: 25px 30px 30px;
}
.data-publicacao, .keywords
{
  display: flex;
    justify-content: center;
    align-items: flex-end;
    flex-direction: column;
}
.botao-eliminar-post
{
    float: right;
    
}
.botao-editar-post
{
    float: right;
    margin-right: 10px;
}
.card-text
{
    color: #aaa;
    margin-left: 20px;
}

.rs-container *{
    box-sizing:border-box;-webkit-touch-callout:none;
    -webkit-user-select:none;-khtml-user-select:none;
    -moz-user-select:none;-ms-user-select:none;user-select:none
    
}
.rs-container{
    font-family:Arial,Helvetica,sans-serif;height:45px;position:relative
    
}
.rs-container .rs-bg,.rs-container .rs-selected{
    background-color:#eee;border:1px solid #ededed;height:10px;left:0;position:absolute;top:5px;width:100%;border-radius:3px
    
}.rs-container .rs-selected{background-color:#00b3bc;border:1px solid #00969b;transition:all .2s linear;width:0}.rs-container.disabled .rs-selected{background-color:#ccc;border-color:#bbb}.rs-container .rs-pointer{background-color:#fff;border:1px solid #bbb;border-radius:4px;cursor:pointer;height:20px;left:-10px;position:absolute;top:0;transition:all .2s linear;width:30px;box-shadow:inset 0 0 1px #FFF,inset 0 1px 6px #ebebeb,1px 1px 4px rgba(0,0,0,.1)}.rs-container.disabled .rs-pointer{border-color:#ccc;cursor:default}.rs-container .rs-pointer::after,.rs-container .rs-pointer::before{content:'';position:absolute;width:1px;height:9px;background-color:#ddd;left:12px;top:5px}.rs-container .rs-pointer::after{left:auto;right:12px}.rs-container.sliding .rs-pointer,.rs-container.sliding .rs-selected{transition:none}.rs-container .rs-scale{left:0;position:absolute;top:5px;white-space:nowrap}.rs-container .rs-scale span{float:left;position:relative}.rs-container .rs-scale span::before{background-color:#ededed;content:"";height:8px;left:0;position:absolute;top:10px;width:1px}.rs-container.rs-noscale span::before{display:none}.rs-container.rs-noscale span:first-child::before,.rs-container.rs-noscale span:last-child::before{display:block}.rs-container .rs-scale span:last-child{margin-left:-1px;width:0}.rs-container .rs-scale span ins{color:#333;display:inline-block;font-size:12px;margin-top:20px;text-decoration:none}.rs-container.disabled .rs-scale span ins{color:#999}.rs-tooltip{color:#333;width:auto;min-width:60px;height:30px;background:#fff;border:1px solid #00969b;border-radius:3px;position:absolute;transform:translate(-50%,-35px);left:13px;text-align:center;font-size:13px;padding:6px 10px 0}.rs-container.disabled .rs-tooltip{border-color:#ccc;color:#999}

ul {
  list-style-type: none;
}
</style>




<?php
/**
 * This file is used for listing the posts on profile
 *
 * @package buddyblog
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$user_id       = bp_displayed_user_id();
$is_my_profile = bp_is_my_profile();

$count_post = count_user_posts( $user_id );

$numOfCols = 2;
$rowCount = 0;
$bootstrapColWidth = 12 / $numOfCols;


?>






<?php if ( buddyblog_user_has_posted( $user_id, $is_my_profile ) ): ?>
<?php
    //let us build the post query
    if ( $is_my_profile || is_super_admin() ) {
 		$status = 'any';
	} else {
		$status = 'publish';
	}
	
    $paged = bp_action_variable( 1 );
    $paged = $paged ? $paged : 1;
    
    
    
	
	?>
	
	
<form role="search" method="get" id="searchform" class="searchform">

<label class="hidden" for="s"><?php _e('Search for:'); ?></label>

<div><input type="text" value="<?php the_search_query(); ?>" name="s" id="s" />

<br>
<strong>Categorias: </strong>
<?php
// generate list of categories
$tags = get_categories();
foreach ($tags as $tag) {
    echo 
        '<label>',
        '<input type="checkbox" name="taglist[]" value="',  $tag->cat_ID, '" /> ',
        $tag->name,
        "</label>\n";
}
?>
<br>
<strong>Keywords: </strong>
<?php
// generate list of categories
$keywords = get_tags();

foreach ($keywords as $keyword) {
    echo 
        '<label>',
        '<input type="checkbox" name="keylist[]" value="',  $keyword->slug, '" /> ',
        $keyword->name,
        "</label>\n";
}
?>
<br>

<p>Rating: (Pontuação)</p>
<br>
<div class="slider-container">
<input type="text" id="slider2" class="slider" name="slider2" />
	</div>

    <script>
      
      
              (function () {
            'use strict';

            var init = function () {                

                var slider2 = new rSlider({
                    target: '#slider2',
                    values: [0, 1, 2, 3, 4, 5],
                    range: true,
                    set: [0,5],
                    scale: true,
                    labels: true,
                });

            };
            window.onload = init;
        })();
    
    </script>

<br>
<input type="submit" id="searchsubmit" value="Search" />

</div>



</form>

<?php
$checkboxArray = $_GET['taglist'];
$catIds = implode(',',$checkboxArray);

$checkboxArray2 = $_GET['keylist'];
$tagIds = implode(',',$checkboxArray2);



if($_GET['slider2'] != "")
{
    $range_num = $_GET['slider2'];
    
    $args = array(
    'author'        => $user_id,
		'post_type'     => buddyblog_get_posttype(),
		'post_status'   => $status,
		'paged'         => intval( $paged ),
		's' => $_GET['s'],
		'cat' => $catIds,
		'tag' => $tagIds,
		'posts_per_page' => 6,
            'meta_query' => array(
    		array(
    			'key' => 'mrp_rating_result_1_star_rating',
    			'value' => $range_num,
    			'compare' => 'BETWEEN'
    		)
    	)
    );
}
else
{
    $args = array(
    'author'        => $user_id,
		'post_type'     => buddyblog_get_posttype(),
		'post_status'   => $status,
		'paged'         => intval( $paged ),
		's' => $_GET['s'],
		'cat' => $catIds,
		'tag' => $tagIds,
		'posts_per_page' => 6
    );
    
    
}



?>


<?php

    /*$query_args = array(
		'author'        => $user_id,
		'post_type'     => buddyblog_get_posttype(),
		'post_status'   => $status,
		'paged'         => intval( $paged ),
		's' => $_GET['s'],
		'cat' => $catIds,
		'tag' => $tagIds,
		'orderby' => 'title',
        'order' => 'ASC'
    );*/

	//do the query
    //query_posts( $args );
    
    

// The Query

$query = new WP_Query($args);
global $wp_query;
// Put default query object in a temp variable
$tmp_query = $wp_query;
// Now wipe it out completely
$wp_query = null;
// Re-populate the global with our custom query
$wp_query = $query;
    
?>


    <div class="row">
	<?php if ( $query->have_posts() ): ?>
	

		<?php while ( $query->have_posts() ): $query->the_post();
			global $post;
		?>
		
		<div class="col-md-<?php echo $bootstrapColWidth; ?>">
		    <div class="box2 card">
                <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    
                    
                    <!-- Imagem de Destaque -->
                    <?php if ( function_exists( 'has_post_thumbnail' ) && has_post_thumbnail( get_the_ID() ) ):?>
                        
                        <div class="post-featured-image">
                            <?php  the_post_thumbnail();?>
                        </div>

                    <?php endif;?>
                    
                    <div class="card-body">
                        
                        
                        <div class="post-content">
                            
                            
                            <h5 class="card-title"> <a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'buddyblog' ); ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a> </h5>
                            
                            
                            <?php echo do_shortcode( '[mrp_rating_result post_id="'.$post->ID.'" no_rating_results_text=""]' ); ?>

                            
                            
                            <div class="author-box">
                                
                                <div class="row">
                                    <div class="col-md-6 avatar-img">
                                        <?php echo get_avatar( get_the_author_meta( 'user_email' ), '50' ); ?> 
                                        <span>
                                            <?php printf( _x( ' %s', 'Post written by...', 'buddyblog' ), bp_core_get_userlink( $post->post_author ) ); ?> 
                                        </span>
                                    </div>
                                    
                                    <div class="col-md-6 data-publicacao">
                                         <?php printf( __( '<span class="icon_data_pub">%1$s</span>', 'buddyblog' ), get_the_date()); ?>
                                        
                                    </div>
                                </div>
                                
                                <div style="margin-top: 30px"></div>
                                
                                
                                <div class="row">
                                
                                    <div class="col-md-6">
                                        
                                        
                                        <?php printf( __( '<i class="fa fa-bookmark" aria-hidden="true"></i> <b>Categoria(s):</b> %1$s ', 'buddyblog' ), get_the_category_list( ', ' )); ?>
                                    </div>
                                    
                                    <div class="col-md-6 keywords">
                                        <?php the_tags( '<span class="tags">' . __( '<i class="fa fa-tags" aria-hidden="true"></i> <b>Keywords:</b> ', 'buddyblog' ), ', ', '' ); ?> 
                                    </div>
                                        
                                </div>
                                
                                <div style="margin-top: 40px"></div>
            
                                <?php if ( is_sticky() ) : ?>
                                    <span class="activity sticky-post"><?php _ex( 'Featured', 'Sticky post', 'buddyblog' ); ?></span>
                                <?php endif; ?>
                                
                                
                            </div>
                            
                            
                            
                            
        
                            <div class="entry card-text">
                                
                                <?php
                                    
                                    $excerpt = mb_strimwidth( strip_tags(get_the_content()), 0, 200, '...' );
                                    echo $excerpt;

                                    
                                    
                                    
                                ?>
                            </div>
        
                            <p class="postmetadata">
                            
                            <span class="comments"><br><?php comments_popup_link( __( 'No Comments &#187;', 'buddyblog' ), __( '1 Comment &#187;', 'buddyblog' ), __( '% Comments &#187;', 'buddyblog' ) ); ?></span></p>
        
                                
                        </div>
                        
                            <div class="post-actions card-footer">
                                <?php echo buddyblog_get_post_publish_unpublish_link( get_the_ID() );?>
                                <?php echo buddyblog_get_delete_link();?>
                                <?php echo buddyblog_get_edit_link();?>
                                
                            </div> 
                        
                    </div>
                    
                    
                    
                    
                    

                



			</div>
        </div>
        
        </div>
		
		
        <?php
            $rowCount++;
            if($rowCount % $numOfCols == 0) echo '</div><div class="row">';
        ?>
            
                   
        <?php endwhile;?>
        </div>
        
        
            <div class="pagination">
                <?php buddyblog_paginate(); ?>
            </div>
    <?php else: ?>
            <p><?php _e( 'There are no posts by this user at the moment. Please check back later!', 'buddyblog' );?></p>
    <?php endif; ?>

    <?php 
       wp_reset_postdata();
       wp_reset_query();
    ?>
    
    
    
    
    
     

<?php elseif ( bp_is_my_profile() && buddyblog_user_can_post( get_current_user_id() ) ): ?>
    <p> <?php _e( "You haven't posted anything yet.", 'buddyblog' );?> <a href="<?php echo buddyblog_get_new_url();?>"> <?php _e( 'New Post', 'buddyblog' );?></a></p>

<?php elseif ( bp_is_user() ): ?>
    <?php echo sprintf( "<p>%s haven't posted anything yet.</p>", bp_get_displayed_user_fullname() );?>

<?php endif; ?>



<script>
    jQuery('a:contains("Edit")').addClass('btn btn-info botao-editar-post');
    jQuery('.botao-editar-post').prepend('<i class="fa fa-plus" aria-hidden="true"></i> ');
    
    jQuery('a:contains("Delete")').addClass('btn btn-danger botao-eliminar-post');
    jQuery('.botao-eliminar-post').prepend('<i class="fa fa-trash" aria-hidden="true"></i> ');
    
    
    jQuery('a:contains("Unpublish")').addClass('btn btn-primary botao-unpublish-post');
    jQuery('.botao-unpublish-post').prepend('<i class="fa fa-arrow-up"></i> ');
    
    jQuery('a:contains("Publish")').addClass('btn btn-primary botao-publish-post');
    jQuery('.botao-publish-post').prepend('<i class="fa fa-arrow-down" aria-hidden="true"></i> ');
    
    jQuery('.post-featured-image > img').addClass('img-responsive center-block d-block mx-auto card-img-top');
    jQuery('.post-featured-image > img').css('width', '100%');
    jQuery('.post-featured-image > img').css('border-radius', '10px');
    
    
    jQuery('.avatar-img > img').css('vertical-align','middle');
    jQuery('.icon_data_pub').prepend('<i class="fa fa-calendar"></i> ');
</script>
















