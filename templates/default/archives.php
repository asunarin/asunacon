<?php $this->show_header();?>

        <div id="content-wide">
			
			<div class="archives-block">
				
				<h2>标签云</h2>
				<?php $this->show_tags_list();?>
				
			</div>
			
			<div class="archives-block">

				<h2>日期归档</h2>
				<?php $this->show_dates_list();?>
				
			</div>

            <div class="clear"></div>
        </div>

<?php $this->show_footer(); ?>
