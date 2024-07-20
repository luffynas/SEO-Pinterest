<div class="wrap">
    <h1>Redirections</h1>
    <form method="POST" class="form-inline mb-3">
        <!-- <input type="text" name="search" class="form-control me-2" placeholder="Search Redirection" /> -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRedirectionModal">Tambah Redirection</button>
    </form>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nama Redirection</th>
                <th>Source Redirection</th>
                <th>Destination Redirection</th>
                <th>Random Post</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            foreach ( $redirections as $redirection ) : ?>
                <tr>
                    <td><?php echo esc_html( $redirection['name'] ); ?></td>
                    <td><?php echo esc_html( $redirection['source'] ); ?></td>
                    <td><?php echo $redirection['random_post'] ? 'N/A' : $redirection['destination']; ?></td>
                    <td><?php echo $redirection['random_post'] ? 'TRUE' : 'FALSE'; ?></td>
                    <td>
                        <button type="button" class="btn btn-danger delete-redirection" data-id="<?php echo esc_attr( $redirection['id'] ); ?>">Hapus</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="addRedirectionModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Redirection</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="save_redirection">
                    <div class="mb-3">
                        <label for="redirection_name" class="form-label">Nama Redirection</label>
                        <input type="text" class="form-control" id="redirection_name" name="redirection_name" placeholder="Nama Redirection" required>
                    </div>
                    <div class="mb-3">
                        <label for="source_redirection" class="form-label">Source Redirection</label>
                        <input type="text" class="form-control" id="source_redirection" name="source_redirection" placeholder="Source Redirection" required>
                    </div>
                    <div class="mb-3">
                        <label for="random_post" class="form-label">Random Post</label>
                        <select class="form-select" id="random_post" name="random_post">
                            <option value="true" selected>TRUE</option>
                            <option value="false">FALSE</option>
                        </select>
                    </div>
                    <div class="mb-3" id="destination_redirection_group">
                        <label for="destination_redirection" class="form-label">Destination Redirection</label>
                        <input type="text" class="form-control" id="destination_redirection" name="destination_redirection" placeholder="Destination Redirection">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>
