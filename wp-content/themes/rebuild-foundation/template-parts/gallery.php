<!-- //// Gallery or Image-->

<?php 
$images = get_field( 'post_gallery' );

if( $images ): ?>

    <div class="gallery">
        <?php foreach( $images as $image ): ?>

            <figure class="slide-item">

                <img src="<?php echo $image['sizes']['large']; ?>" alt="<?php echo $image['alt']; ?>" />

                <figcaption><?php echo $image['caption']; ?></figcaption>

            </figure>

        <?php endforeach; ?>
    </div>

<?php elseif( has_post_thumbnail() ): ?>

     <div class="single-image">

        <figure class="entry-thumbnail">

            <?php the_post_thumbnail( 'full' ); ?>

            <?php $caption = rebuild_get_the_feature_caption(); ?>

            <figcaption><?php echo ( $caption ) ? $caption : ''; ?></figcaption>

        </figure>

    </div>

<?php endif; ?>
