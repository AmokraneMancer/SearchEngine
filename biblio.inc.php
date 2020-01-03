
<?php

///////suppression de code-script
function strip_scripts($chaine_html)
{
  $model_balises_scripts = '/<script[^>]*?>.*?<\/script>/is';
  $html_sans_scripts = preg_replace($model_balises_scripts, '', $chaine_html);
  return $html_sans_scripts;
}


/////////separe les mot dun texte
function explodebis($separateur, $texte)
{
  $token = strtok($texte, $separateur);

  if (strlen($token) > 2) $tab_elements[] = $token;

  while ($token = strtok($separateur)) {
    if (strlen($token) > 2) $tab_elements[] = $token;
  }
  return $tab_elements;
}

////////////////////////get title
function get_title($fichier_html)
{

  $string = file_get_contents($fichier_html);

  $modele = '/<title[^>]*>(.*)<\/title>/is';

  preg_match($modele, $string, $tab_titre); // permet d'evaluer le model les occurences  
  return ($tab_titre[1]);
}




////////////////////////getbody
function get_body($fichier_html)
{

  $string = file_get_contents($fichier_html);

  $modele = '/<body[^>]*>(.*)<\/body>/is';

  preg_match($modele, $string, $tab_titre); // permet d'evaluer le model les occurences  
  return ($tab_titre[1]);
}



/////////////////////////keywords
function getKeywords($fichier_html)
{
  $tab_metas = get_meta_tags($fichier_html);
  return $tab_metas["keywords"];
}


///////////////////////description
function getDescription($fichier_html)
{

  $tab_metas = get_meta_tags($fichier_html);
  return $tab_metas["description"];
}


function print_tab($tab)
{
  foreach ($tab as $key => $value)
    echo "$key : $value <br>";
}


// function CaractHTML2ASCII (fichier_html);
// {


// }

?>
