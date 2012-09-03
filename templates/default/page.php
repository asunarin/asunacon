<?php $this->show_header($page->title);?>

    <div id="content-wide">

        <div class="hentry">
			
            <div class="entry-content">
				
				<?php $this->show_pages_list();?>
                <?php __($page->content); ?>
                
            </div>

        </div>

    </div>

<?php $this->show_footer(); ?>
