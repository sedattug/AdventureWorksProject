<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="tr-tr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">

    <title>{site_title}</title>
    <!-- FAVICONS -->
    <link rel="shortcut icon" href="{favicon_url}" type="image/x-icon">
        <link rel="icon" href="{favicon_url}" type="image/x-icon">
            <meta name="description" content="{site_description}">
            <meta name="author" content="(c) 2019 Developers: Sedat Tuğ">

            <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
            {css}
            {css_link}
            {/css}
            <!-- Basic Styles -->
            {page_default_css_array}
            {page_default_css}
            {/page_default_css_array}

            {page_default_header_js_array}
            {page_default_header_js}
            {/page_default_header_js_array}

            <?php
            if (isset($this->xajax)) :
                $this->xajax->printJavascript();
            endif;
            ?> 

            </head>

            <body >  
            <div class='lmask' style="display:none;"></div>
            <div class="se-pre-con"></div>
            <!-- Navigation -->
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
                <div class="container">
                    <a class="navbar-brand" href="<?php echo base_url(); ?>">{site_title}</a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarResponsive">
                        <ul class="navbar-nav ml-auto">
                            <li class="nav-item active">
                                <a class="nav-link" href="#">Home
                                    <span class="sr-only">(current)</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">About</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">Services</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">Contact</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <div class="container">

                <div class="row">

                    <div class="col-lg-3">

                        <h1 class="my-4">Categories</h1>
                        <div class="accordion" id="accordionExample">
                            <?php if(isset($data['productcategories']) && is_array($data['productcategories']) && count($data['productcategories']) > 0):?>
                            <?php foreach ($data['productcategories'] as $category => $subcategories): ?>
                            <div class="card">
                                <div class="card-header" id="heading-<?php echo $category;?>">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse-<?php echo $category;?>" aria-expanded="true" aria-controls="collapse-<?php echo $category;?>">
                                            <?php echo $category ?>
                                        </button>
                                    </h2>
                                </div>

                                <div id="collapse-<?php echo $category;?>" class="collapse <?php echo ((string) $category === 'Accessories' ? 'show' : '')?>" aria-labelledby="heading-<?php echo $category;?>" data-parent="#accordionExample">
                                    <div class="card-body">
                                        <ul>
                                        <?php foreach($subcategories as $subcategory): ?>
                                            <li><a target="_blank" href="<?php echo base_url('subcategories/') . $subcategory->productcategoryid . '/' . $subcategory->productsubcategoryid?>"><?php echo $subcategory->productsubcategoryname; ?></a></li>
                                        <?php endforeach;?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach;?>
                            <?php endif; ?>
                        </div>


                    </div>
                    <!-- /.col-lg-3 -->