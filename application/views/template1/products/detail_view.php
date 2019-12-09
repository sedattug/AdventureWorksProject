<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

defined('BASEPATH') || exit('No direct script access allowed');
//dump($productinfo);
//dump($productreviews);
?>
<div class="col-lg-9">
    <nav aria-label="breadcrumb" style="margin-top:30px; ;">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><?php echo $productinfo->productcategoryname; ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo $productinfo->productsubcategoryname; ?></li>
        </ol>
    </nav>
    <div class="card mt-4">
        <img class="card-img-top img-fluid" src="http://placehold.it/900x400" alt="">
            <div class="card-body">
                <h3 class="card-title"><?php echo $productinfo->productname; ?></h3>
                <h4>$<?php echo $productinfo->listprice; ?></h4>
                <ul>
                    <li><span><strong>Color :</strong> <?php echo $productinfo->color; ?></span></li>
                    <li><span><strong>Class :</strong> <?php echo product_class($productinfo->class); ?></span></li>
                    <li><span><strong>Productline :</strong> <?php echo product_line($productinfo->productline); ?></span></li>
                    <li><span><strong>Style :</strong> <?php echo product_style($productinfo->style); ?></span></li>
                </ul>
                <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Sapiente dicta fugit fugiat hic aliquam itaque facere, soluta. Totam id dolores, sint aperiam sequi pariatur praesentium animi perspiciatis molestias iure, ducimus!</p>
                <span class="text-warning">&#9733; &#9733; &#9733; &#9733; &#9734;</span>
                4.0 stars <span class="pull-right"><strong>Product Number :</strong> <?php echo $productinfo->productnumber; ?></span>
            </div>
    </div>
    <!-- /.card -->

    <div class="card card-outline-secondary my-4">
        <div class="card-header">
            Product Reviews
        </div>
        <div class="card-body">
            <?php if (isset($productreviews) && is_array($productreviews) && count($productreviews) > 0): ?>
                <?php foreach ($productreviews as $productreview):
                    ?>
                    <?php if ((integer) $productreview->reviewstatus === (integer) PREVIEW_STATUS::APPROVED) { ?>
                        <p class="comment-summary-<?php echo $productreview->productid . '-' . $productreview->productreviewid; ?>">
                            <?php echo comment_summary($productreview->comments); ?> <a data-productid = "<?php echo $productreview->productid; ?>" data-reviewid = "<?php echo $productreview->productreviewid; ?>" data-type = 'summary' href="javascript:void(0);" class="togglecomment"> More</a>
                        </p>
                        <p style="display:none;" class="comment-detail-<?php echo $productreview->productid . '-' . $productreview->productreviewid; ?>">
                            <?php echo $productreview->comments; ?> <a data-productid = "<?php echo $productreview->productid; ?>" data-reviewid = "<?php echo $productreview->productreviewid; ?>" data-type = 'detail' href="javascript:void(0);" class="togglecomment"> Less</a>
                        </p>
                    <?php } else { ?>
                        <?php echo preview_status_text($productreview->reviewstatus); ?>
                    <?php } ?>
                    <small class="text-muted">Posted by <?php echo $productreview->reviewername; ?> on <?php echo $productreview->reviewdate; ?></small>
                    <hr>
                <?php endforeach; ?>
            <?php endif; ?>
            <div class="row">
                <div class="col-md-12">
                    <p>
                        <button class="btn btn-primary btn-block" type="button" data-toggle="collapse" data-target="#leaveReview" aria-expanded="false" aria-controls="leaveReview">
                            Leave a Review
                        </button>
                    </p>
                </div>
            </div>
            <div class="collapse" id="leaveReview">
                <div class="card card-body" style="border:none;">
                    <?php echo form_open(base_url(), $attributes = array('name' => 'leave-review', 'id' => 'leave-review')); ?>
                    <input type="hidden" name="productid" id="productid" value="<?php echo $productinfo->productid; ?>">
                        <div class="form-group">
                            <label for="email">Email address</label>
                            <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp" placeholder="Enter email">
                                <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
                        </div>
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Your Name">
                        </div>
                        <div class="form-group">
                            <label for="review">Your Review</label>
                            <textarea class="form-control" id="review" name="review" rows="3"></textarea>
                        </div>
                        <div class="form-group form-inline">
                            <span id="image_captcha" class="mb-2 mr-sm-2"><?php echo $captchaImg; ?></span>
                            <div class="input-group mb-2 mr-sm-2">
                                <div class="input-group-prepend refreshCaptcha">
                                    <div class="input-group-text"><i class="fa fa-refresh" aria-hidden="true"></i></div>
                                </div>
                                <input type="text"  name="captcha" class="form-control" id="captcha" placeholder="Captcha" maxlength="5">
                            </div>
                        </div>
                        <button  type="submit" class="btn btn-dark btn-block"> Send Review </button>
                        <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>
    <!-- /.card -->

</div>
<!-- /.col-lg-9 -->
<script>
    $("#leave-review").submit(function (e) {
        e.preventDefault();
        mask_div('leaveReview', 'Please Wait...')
        xajax_xajaxLeaveReview(xajax.getFormValues('leave-review'));
    });

    $(".togglecomment").click(function () {
        var type = $(this).attr("data-type");
        var productid = $(this).attr("data-productid");
        var reviewid = $(this).attr("data-reviewid");
        if (type === 'summary') {
            $(".comment-summary-" + productid + "-" + reviewid).hide();
            $(".comment-detail-" + productid + "-" + reviewid).show();
        } else {
            $(".comment-detail-" + productid + "-" + reviewid).hide();
            $(".comment-summary-" + productid + "-" + reviewid).show();
        }
    });

    $(".refreshCaptcha").click(function () {
        xajax_xajaxRefreshCaptcha();
    });
</script>
