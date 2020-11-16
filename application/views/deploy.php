<?php
// Repository Update
shell_exec('/usr/local/cpanel/bin/uapi VersionControl update name=dev_repo repository_root=/home/webapi/public_html/staging branch=dev source_repository=\'{"remote_name":"origin"}\'
');
// Deploy
shell_exec('/usr/local/cpanel/bin/uapi VersionControlDeployment create repository_root=/home/webapi/public_html/staging');
echo "done";
?>