            <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">CMS /</span> File List</h4>
            <div class="card py-3 mb-4">
                <form id="filter">
                <div class="card-body">
                    <div class="mb-3 row">
                        <div class="col-md-6 row">
                            <label for="filename" class="col-md-2 col-form-label">FileName</label>
                            <div class="col-md-10">
                            <input class="form-control" type="text" value="" name="filename">
                            </div>
                        </div>
                        <div class="offset-md-1 col-md-5">
                            <div class="form-check form-switch mb-2 ">
                                <input class="form-check-input" type="checkbox" name="published" checked="">
                                <label class="form-check-label" for="published">Published</label>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
            </div>
            <div class="card py-3 mb-4">
                <div class="card-header">
                    <a type="button" class="btn btn-info" href="/CMS/edit"><i class="menu-icon tf-icons bx bx-plus"></i> Add</a>
                </div>
                <div class="card-body">
                    <table class="table" id="datatable">
                        <thead>
                        <tr class="text-nowrap">
                            <th>Action</th>
                            <th>File Name</th>
                            <th>Create At</th>
                            <th>Avalible From</th>
                            <th>Avalible To</th>
                            <th>Published</th>
                        </tr>
                        </thead>
                        <tbody>
                        
                        </tbody>
                    </table>
                </div>
            </div>