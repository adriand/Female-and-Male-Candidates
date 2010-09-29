<?
  
  // a simple way to get around the same origin policy that prevents cross-domain Javascript
  // AJAX requests: build a quick and dirty PHP method of getting them and rendering them.
  // to keep our Javascript less verbose, we'll only ever ask for stuff after the API address
  if ($_GET['api']) {
    echo file_get_contents("http://elections.raisethehammer.org/api" . $_GET['api']);
    exit;
  }
  
?>