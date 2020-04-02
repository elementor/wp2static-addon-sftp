<h2>sFTP Deployment Options</h2>

<form
    name="wp2static-sftp-save-options"
    method="POST"
    action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">

    <?php wp_nonce_field( $view['nonce_action'] ); ?>
    <input name="action" type="hidden" value="wp2static_sftp_save_options" />


<table class="widefat striped">
    <tbody>

        <tr>
            <td style="width:50%;">
                <label
                    for="<?php echo $view['options']['host']->name; ?>"
                ><?php echo $view['options']['host']->label; ?></label>
            </td>
            <td>
                <input
                    id="<?php echo $view['options']['host']->name; ?>"
                    name="<?php echo $view['options']['host']->name; ?>"
                    type="text"
                    value="<?php echo $view['options']['host']->value !== '' ? $view['options']['host']->value : ''; ?>"
                />
            </td>
        </tr>

        <tr>
            <td style="width:50%;">
                <label
                    for="<?php echo $view['options']['port']->name; ?>"
                ><?php echo $view['options']['port']->label; ?></label>
            </td>
            <td>
                <input
                    id="<?php echo $view['options']['port']->name; ?>"
                    name="<?php echo $view['options']['port']->name; ?>"
                    type="text"
                    value="<?php echo $view['options']['port']->value !== '' ? $view['options']['port']->value : ''; ?>"
                />
            </td>
        </tr>

        <tr>
            <td style="width:50%;">
                <label
                    for="<?php echo $view['options']['username']->name; ?>"
                ><?php echo $view['options']['username']->label; ?></label>
            </td>
            <td>
                <input
                    id="<?php echo $view['options']['username']->name; ?>"
                    name="<?php echo $view['options']['username']->name; ?>"
                    type="text"
                    value="<?php echo $view['options']['username']->value !== '' ? $view['options']['username']->value : ''; ?>"
                />
            </td>
        </tr>

        <tr>
            <td style="width:50%;">
                <label
                    for="<?php echo $view['options']['password']->name; ?>"
                ><?php echo $view['options']['password']->label; ?></label>
            </td>
            <td>
                <input
                    id="<?php echo $view['options']['password']->name; ?>"
                    name="<?php echo $view['options']['password']->name; ?>"
                    type="password"
                    value="<?php echo $view['options']['password']->value !== '' ?
                        \WP2Static\Controller::encrypt_decrypt('decrypt', $view['options']['password']->value) :
                        ''; ?>"
                />
            </td>
        </tr>

        <tr>
            <td style="width:50%;">
                <label
                    for="<?php echo $view['options']['private_key']->name; ?>"
                ><?php echo $view['options']['private_key']->label; ?></label>
            </td>
            <td>
                <input
                    id="<?php echo $view['options']['private_key']->name; ?>"
                    name="<?php echo $view['options']['private_key']->name; ?>"
                    type="text"
                    value="<?php echo $view['options']['private_key']->value !== '' ? $view['options']['private_key']->value : ''; ?>"
                />
            </td>
        </tr>

        <tr>
            <td style="width:50%;">
                <label
                    for="<?php echo $view['options']['passphrase']->name; ?>"
                ><?php echo $view['options']['passphrase']->label; ?></label>
            </td>
            <td>
                <input
                    id="<?php echo $view['options']['passphrase']->name; ?>"
                    name="<?php echo $view['options']['passphrase']->name; ?>"
                    type="password"
                    value="<?php echo $view['options']['passphrase']->value !== '' ?
                        \WP2Static\Controller::encrypt_decrypt('decrypt', $view['options']['passphrase']->value) :
                        ''; ?>"
                />
            </td>
        </tr>

        <tr>
            <td style="width:50%;">
                <label
                    for="<?php echo $view['options']['remote_root']->name; ?>"
                ><?php echo $view['options']['remote_root']->label; ?></label>
            </td>
            <td>
                <input
                    id="<?php echo $view['options']['remote_root']->name; ?>"
                    name="<?php echo $view['options']['remote_root']->name; ?>"
                    type="text"
                    value="<?php echo $view['options']['remote_root']->value !== '' ? $view['options']['remote_root']->value : ''; ?>"
                />
            </td>
        </tr>

        <tr>
            <td style="width:50%;">
                <label
                    for="<?php echo $view['options']['group']->name; ?>"
                ><?php echo $view['options']['group']->label; ?></label>
            </td>
            <td>
                <input
                    id="<?php echo $view['options']['group']->name; ?>"
                    name="<?php echo $view['options']['group']->name; ?>"
                    type="text"
                    value="<?php echo $view['options']['group']->value !== '' ? $view['options']['group']->value : ''; ?>"
                />
            </td>
        </tr>

        <tr>
            <td style="width:50%;">
                <label
                    for="<?php echo $view['options']['owner']->name; ?>"
                ><?php echo $view['options']['owner']->label; ?></label>
            </td>
            <td>
                <input
                    id="<?php echo $view['options']['owner']->name; ?>"
                    name="<?php echo $view['options']['owner']->name; ?>"
                    type="text"
                    value="<?php echo $view['options']['owner']->value !== '' ? $view['options']['owner']->value : ''; ?>"
                />
            </td>
        </tr>

        <tr>
            <td style="width:50%;">
                <label
                    for="<?php echo $view['options']['dir_permissions']->name; ?>"
                ><?php echo $view['options']['dir_permissions']->label; ?></label>
            </td>
            <td>
                <input
                    id="<?php echo $view['options']['dir_permissions']->name; ?>"
                    name="<?php echo $view['options']['dir_permissions']->name; ?>"
                    type="text"
                    value="<?php echo $view['options']['dir_permissions']->value !== '' ? $view['options']['dir_permissions']->value : ''; ?>"
                />
            </td>
        </tr>

        <tr>
            <td style="width:50%;">
                <label
                    for="<?php echo $view['options']['file_permissions']->name; ?>"
                ><?php echo $view['options']['file_permissions']->label; ?></label>
            </td>
            <td>
                <input
                    id="<?php echo $view['options']['file_permissions']->name; ?>"
                    name="<?php echo $view['options']['file_permissions']->name; ?>"
                    type="text"
                    value="<?php echo $view['options']['file_permissions']->value !== '' ? $view['options']['file_permissions']->value : ''; ?>"
                />
            </td>
        </tr>

    </tbody>
</table>

<br>

    <button class="button btn-primary">Save sFTP Options</button>
</form>

