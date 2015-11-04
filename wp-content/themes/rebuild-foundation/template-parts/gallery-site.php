<!-- //// Gallery or Image-->

<?php 
$images = get_field( 'post_gallery' );

if( $images ): ?>

    <div class="gallery" id="site-gallery">
        <?php foreach( $images as $image ): ?>

            <figure class="slide-item" style="background-image: url('<?php echo $image['sizes']['large']; ?>')">

                <?php echo ( $image['caption'] ) ? '<figcaption>' . $image['caption'] . '</figcaption>' : '' ?>

            </figure>

        <?php endforeach; ?>
    </div>

<?php elseif( has_post_thumbnail() ): ?>

     <div class="single-image">

        <figure class="entry-thumbnail" style="background-image: url(<?php echo wp_get_attachment_url( get_post_thumbnail_id() ); ?>)">

            <?php $caption = rebuild_get_the_feature_caption(); ?>

            <?php echo ( $caption ) ? '<figcaption>' . $caption . '</figcaption>' : ''; ?>

        </figure>

    </div>

<?php endif; ?>
