            <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">CMS / File List /</span> Edit</h4>
            <div class="card py-3 mb-4">
                <form id="filter" enctype="multipart/form-data" method="post">
                <input class="form-control" type="hidden" name="id" value="<?=$media->id?>">
                <div class="card-body">
                    <div class="mb-3 row">
                        <div class="col-md-6 row">
                            <label for="filename" class="col-md-2 col-form-label">Image</label>
                            <div class="col-md-10">
                            <?php if($media->id != 0){ ?>
                                <button type="button" class="btn btn-info" data-imageModal="/api/media/getMediaFile?id=<?= $media->id?>" ><i class="menu-icon tf-icons bx bx-search"></i> <?=$media->displayName?></button>
                            <?php }else{ ?>
                                <input class="form-control" type="file" name="media" accept="image/*" required>
                            <?php } ?>
                            </div>
                            <div id="floatingInputHelp" class="offset-md-2 form-text">
                            Image Resulotion: 1240x774 px;
                            </div>
                        </div>
                        <div class="offset-md-1 col-md-5">
                            <div class="form-check form-switch mb-2 ">
                                <input class="form-check-input" type="checkbox" name="published" value="1" <?=$media->isPublish == 1 ? "checked" : "" ?>>
                                <label class="form-check-label" for="published">Publish</label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-6 row by-2">
                            <label for="html5-date-input" class="col-md-2 col-form-label">Avalible From</label>
                            <div class="col-md-10">
                            <input class="form-control" type="date" name="avalibleFrom" required value="<?=(new DateTime($media->avalibleFrom))->format('Y-m-d') ?>" >
                            </div>
                        </div>
                        <div class="col-md-6 row by-2">
                            <label for="html5-date-input" class="col-md-2 col-form-label">Avalible To</label>
                            <div class="col-md-10">
                            <input class="form-control" type="date" name="avalibleTo" required value="<?=(new DateTime($media->avalibleTo))->format('Y-m-d')?>" >
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a type="button" class="btn btn-warning" href="./"><i class="menu-icon tf-icons bx bx-arrow-back"></i> Cancel</a>
                    <button type="submit" class="btn btn-info float-end" ><i class="menu-icon tf-icons bx bx-save"></i> Save</button>
                </div>
                </form>
            </div>