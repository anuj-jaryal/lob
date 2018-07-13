<?php 
include('../config.php');
include('top_header.php');
include('header.php'); 
require_once '../core/init.php';

if(!isset($_SESSION['admin_id']) && empty($_SESSION['admin_id']))
{
	header('location:index.php');
}

if(isset($_GET['blog']) && !empty($_GET['blog'])){
	$blog_id = mysqli_real_escape_string($link,$_GET['blog']);
	$blog= get_blog_by_id($blog_id);
	if(!blog){
		header('location:blog.php');
	}
}else{
	header('location:blog.php');
}


$flash_msg="";
if(isset($_POST['submit'])){

	$upload_ok = false;
	if(!empty($_FILES['post-image']['name'])){
		$target_dir = "../uploads/";
		$file_name = time()."_".$_FILES['post-image']['name'];
		$target_file=$target_dir.$file_name;
		if(move_uploaded_file($_FILES["post-image"]["tmp_name"], $target_file)){
			$upload_ok = true;
		}
	}
		$blog_id = $_POST['blog_id'];
		$blog_prev_image = $_POST['prev_post_image'];
		$title = mysqli_real_escape_string($link,$_POST['title']);
		$category = mysqli_real_escape_string($link,$_POST['category']);
		$description = sanitize($_POST['description']);
		$image = ($upload_ok)? $file_name:$blog_prev_image;
		$author = $_SESSION['admin_id'];

	$query = "UPDATE `tbl_blog` SET title='$title',description='$description',category='$category',image='$image' WHERE id =".$blog_id; 
 	if(mysqli_query($link, $query)){
 		header('location:edit-blog.php?blog='.$blog_id);
 	}

}


?>

<div class="inner-wrapper">
	<!-- start: sidebar -->
	<?php include("nav_sidebar.php"); ?>
	<!-- end: sidebar -->

	<section role="main" class="content-body">
		<header class="page-header">
			<h2>Blog</h2>

			<div class="right-wrapper pull-right">
				<ol class="breadcrumbs">
					<li>
						<a href="index.html">
							<i class="fa fa-home"></i>
						</a>
					</li>
					<li><span>Blog</span></li>
					<li><span>Add</span></li>
				</ol>

				<a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
			</div>
		</header>

		<!-- start: page -->

		<div class="row">
			<div class="col-md-12">
				<section class="panel">
					<?php
						if($flash_msg != ""){
					?>
					<div class="alert alert-success">
						<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
					   <?php 
					   echo $flash_msg;
					   $flash_msg="";
					   ?>
					</div>
					<?php
						}
					 ?>
					<div class="panel-body">
					 <form id="post-add" action="" method="post" enctype="multipart/form-data">
					 	<input type="hidden" name="blog_id" value="<?php echo $blog['id']; ?>">
					 	<input type="hidden" name="prev_post_image" value="<?php echo $blog['image']; ?>">
						<div class="form-group">
							 <label for="title">Title:</label>
    						 <input type="text" class="form-control" id="title" name="title" value="<?php echo $blog['title']; ?>">
    						 <p id="title-error" class="error"></p>
						</div>
						<div class="form-group">
							<label for="category">Category:</label>
    						<select class="form-control" id="category" name="category">
    							<option value="">Please select</option>
    							<?php
    							$categories=get_blog_category();
    							if($categories){
    								foreach ($categories as $index => $category) {
    							?>
    								<option value="<?php echo $category['id']; ?>" <?php echo ($blog['category']==$category['id'])?'selected':''; ?> ><?php echo $category['name']; ?></option>
    							<?php
    								}
    							} 
    							?>
    						</select>
    						<p id="category-error" class="error"></p>
						</div>
						<div class="form-group">
							<label for="description">Description:</label>
    						<textarea rows="7" name="description" class="form-control" id="description"><?php echo $blog['description']; ?></textarea>
    						<p id="description-error" class="error"></p>
						</div>
						<div class="form-group" class="col-sm-6">
							<label for="image">Image:</label>
    						<input type="file" name="post-image" class="form-control" id="image">
						</div>
						<?php if(!empty($blog['image'])) { ?>
						<div class="col-sm-6">
							<img src="<?php echo BASE_URL; ?>uploads/<?php echo $blog['image']?>" height="200px" />
						</div>
						<?php
						}
						?>
						<div class="clearfix"></div>
						<div class="form-group" style="margin-top:10px;">
							<button type="submit" class="btn btn-primary" name="submit">Submit</button>
						</div>
					 </form>
					</div>
				</section>
			</div>

		</div>

		<!-- end: page -->
	</section>

	<?php include("footer.php"); ?>
	<script type="text/javascript">
		$(document).on('submit','#post-add',function(){
			var title = $('#title').val().trim();
			var category = $('#category').val();
			var description = $('#description').val().trim();

			$("#title-error").text('');
			$("#category-error").text('');
			$("#description-error").text('');


			var error = false;
			if(title.length<=0){
				$("#title-error").text("Field is required!");
				error=true;
			}
			if(category.length<=0){
				$("#category-error").text("Field is required!");
				error=true;
			}

			if(description.length<=0){
				$("#description-error").text("Field is required!");
				error=true;
			}

			if(error){
				return false;
			}else{
				return true;
			}
		});
	</script>