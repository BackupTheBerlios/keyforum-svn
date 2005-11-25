<?php

function redirect($pagetitle,$maintitle,$maintext,$redirurl,$redirtext,$autoredir=0) {

echo<<<EOF
<html>
<head>
  <title>{$pagetitle}</title>

EOF;

if($autoredir) echo "  <meta http-equiv=\"refresh\" content=\"5;url=".$redirurl."\" />\n";

echo<<<EOF
  <link type="text/css" rel="stylesheet" href="style_page_redirect.css" />
  <link rel="shortcut icon" href="favicon.ico" />
</head>
<body>
<div id="redirectwrap">
  <h4>{$maintitle}</h4>
  <p>{$maintext}</p>
  <p class="redirectfoot">(<a href="{$redirurl}">{$redirtext}</a>)</p>
</div>
</body>
</html>
EOF;

}

?>
