jQuery(document).ready(function($) {
    // Tampilkan modal
    $('[data-bs-toggle="modal"]').on('click', function() {
        var target = $(this).data('bs-target');
        $(target).modal('show');
    });

    // Sembunyikan modal saat klik tombol close
    $('.btn-close').on('click', function() {
        $(this).closest('.modal').modal('hide');
    });

    // Sembunyikan modal saat klik di luar modal
    $(window).on('click', function(event) {
        if ($(event.target).hasClass('modal')) {
            $(event.target).modal('hide');
        }
    });

    // Nonaktifkan atau tampilkan Destination Redirection jika Random Post bernilai TRUE atau FALSE
    $('#random_post').on('change', function() {
        var randomPostValue = $(this).val();
        console.log(`randomPostValue ::: ${randomPostValue}`);
        if (randomPostValue === 'true') {
            $('#destination_redirection_group').hide();
        } else {
            $('#destination_redirection_group').show();
        }
    }).trigger('change'); // Trigger change event pada saat load

    // AJAX untuk menyimpan redirection
    $('#addRedirectionModal form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var data = form.serialize();

        $.post(ajaxurl, data, function(response) {
            if (response.success) {
                window.location.reload();
            } else {
                alert('Terjadi kesalahan: ' + response.data);
            }
        });
    });

    // AJAX untuk menghapus redirection
    $('.delete-redirection').on('click', function(e) {
        e.preventDefault();
        if (!confirm('Apakah Anda yakin ingin menghapus redirection ini?')) {
            return;
        }

        var id = $(this).data('id');
        $.post(ajaxurl, { action: 'delete_redirection', id: id }, function(response) {
            if (response.success) {
                window.location.reload();
            } else {
                alert('Terjadi kesalahan: ' + response.data);
            }
        });
    });
});
