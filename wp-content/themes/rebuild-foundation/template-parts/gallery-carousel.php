<!-- //// Gallery or Image-->

<?php 
$images = get_field( 'post_gallery' );

if( $images ): ?>

    <div class="gallery default-carousel">
        <?php foreach( $images as $image ): ?>

            <figure class="slide-item">

                <img src="<?php echo $image['sizes']['large']; ?>" alt="<?php echo $image['alt']; ?>" />

                <?php echo ( $image['caption'] ) ? '<figcaption>' . $image['caption'] . '</figcaption>' : '' ?>

            </figure>

        <?php endforeach; ?>
    </div>

<?php endif; ?>
