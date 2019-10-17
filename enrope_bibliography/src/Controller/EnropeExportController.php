<?php

namespace Drupal\enrope_bibliography\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\StreamWrapper\PrivateStream;
use Drupal\Core\StreamWrapper\PublicStream;
use Drupal\node\Entity\Node;

class EnropeExportController extends ControllerBase
{

  public function exportRIS($node){

    $actualNode = Node::load($node);
    if($actualNode->getType() == 'annotated_bibliography'){


      $data = $this->getAnnotatedBibliographyNodeFields($actualNode);

      $private_path = PrivateStream::basepath();
      $public_path = PublicStream::basepath();
      $file_base = ($private_path) ? $private_path : $public_path;
      $name = preg_replace('/\s+/', '_', $data['title']);
      $filename = $name. '.ris';
      $filepath = $file_base . '/' . $filename;
      $csvFile = fopen($filepath, "w");

      switch ($data['type']){
        case 'peer_reviewed':
          $type_for_ris = 'JOUR';
          break;
        case 'practitioner_magazine':
          $type_for_ris = 'JOUR';
          break;
        case 'book':
          $type_for_ris = 'BOOK';
          break;
        case 'book chapter':
          $type_for_ris = 'BOOK';
          break;
        case 'unpublished thesis/dissertation':
          $type_for_ris = 'thes';
          break;
        default:
          $type_for_ris = 'GEN';
      }


      fwrite($csvFile,'TY  - '.$type_for_ris . "\n");
      foreach ($data['authors'] as $author){
        fwrite($csvFile, 'AU  - '.$author['value']. "\n");
      }
      fwrite($csvFile, 'TI  - '.$data['title']. "\n");

      if (!empty($data['year'])) fwrite($csvFile, 'PY  - '.$data['year']. "\n");
      if (!empty($data['link'])) fwrite($csvFile, 'UR  - '.$data['link']. "\n");
      if (!empty($data['doi'])) fwrite($csvFile, 'DO  - '.$data['doi']. "\n");
      if (!empty($data['isbn'])) fwrite($csvFile, 'SN  - '.$data['isbn']. "\n");
      if (!empty($data['abstract'])) fwrite($csvFile, 'AB  - '.$data['abstract']. "\n");
      if (!empty($data['language'])) fwrite($csvFile, 'LA  - '.$data['language']. "\n");
      fwrite($csvFile, 'ER  -');

      fclose($csvFile);

      header('Content-Type: application/x-research-info-systems');
      header('Content-Disposition: attachment; filename="'. basename($filepath) . '";');
      header('Content-Length: ' . filesize($filepath));
      readfile($filepath);
      unlink($filepath);
    }

    exit;
  }


  public function exportBIB($node){

    $actualNode = Node::load($node);
    if($actualNode->getType() == 'annotated_bibliography'){


      $data = $this->getAnnotatedBibliographyNodeFields($actualNode);

      $private_path = PrivateStream::basepath();
      $public_path = PublicStream::basepath();
      $file_base = ($private_path) ? $private_path : $public_path;
      $name = preg_replace('/\s+/', '_', $data['title']);
      $filename = $name. '.bib';
      $filepath = $file_base . '/' . $filename;
      $csvFile = fopen($filepath, "w");

      switch ($data['type']){
        case 'peer_reviewed':
          $type_for_ris = '@misc{';
          break;
        case 'practitioner_magazine':
          $type_for_ris = '@misc{';
          break;
        case 'book':
          $type_for_ris = '@book{';
          break;
        case 'book chapter':
          $type_for_ris = '@incollection{';
          break;
        case 'unpublished thesis/dissertation':
          $type_for_ris = '@misc{';
          break;
        default:
          $type_for_ris = '@misc{';
      }


      fwrite($csvFile,$type_for_ris . "\n");

      $i = 0;
      $c = count($data['authors']);

      $authors = array_column($data['authors'], 'value');


      foreach ($authors as $key => $val) {
        if ($i++ < $c - 1) {
          $authors[$key] .= ' and ';
        }
      }

      $authors_string = join($authors);


      fwrite($csvFile, 'author = "'.$authors_string.'"'. ",\n");
      fwrite($csvFile, 'title = "'.$data['title'].'"'. ",\n");

      if (!empty($data['year'])) fwrite($csvFile, 'year = "'.$data['year'].'"'. ",\n");
      if (!empty($data['doi'])) fwrite($csvFile, 'doi = "'.$data['doi'].'"'. ",\n");
      if (!empty($data['abstract'])) fwrite($csvFile, 'abstract = "'.$data['abstract'].'"'. ",\n");
      if (!empty($data['annotation'])) fwrite($csvFile, 'annote = "'.$data['annotation'].'"'. ",\n");
      if (!empty($data['language'])) fwrite($csvFile, 'language = "'.$data['language'].'"'. ",\n");
      if (!empty($data['link'])) fwrite($csvFile, 'url = "'.$data['link'].'"'. ",\n");
      fwrite($csvFile, '}');

      fclose($csvFile);

      header('Content-Type: application/x-research-info-systems');
      header('Content-Disposition: attachment; filename="'. basename($filepath) . '";');
      header('Content-Length: ' . filesize($filepath));
      readfile($filepath);
      unlink($filepath);
    }

    exit;
  }

  private function getAnnotatedBibliographyNodeFields($actualNode){
    $title = $actualNode->get('field_title_in_the_original_lang')->first()->getString();
    $authors = !$actualNode->get('field_author_name_s_initial_s_')->isEmpty() ? $actualNode->get('field_author_name_s_initial_s_')->getValue() : '';
    $year = !$actualNode->get('field_year')->isEmpty() ? $actualNode->get('field_year')->first()->getString() : '';
    $link = !$actualNode->get('field_link_to_paper_or_doi')->isEmpty() ? $actualNode->get('field_link_to_paper_or_doi')->first()->getString() : '';
    $doi = !$actualNode->get('field_doi')->isEmpty() ? $actualNode->get('field_doi')->first()->getString() : '';
    $isbn = !$actualNode->get('field_isbn')->isEmpty() ? $actualNode->get('field_isbn')->first()->getString() : '';
    $abstract = !$actualNode->get('field_an_abstract_in_english')->isEmpty() ? $actualNode->get('field_an_abstract_in_english')->first()->getString() : '';
    $annotation = !$actualNode->get('field_annotation_in_english')->isEmpty() ? $actualNode->get('field_annotation_in_english')->first()->getString() : '';
    $type = !$actualNode->get('field_type')->isEmpty() ? $actualNode->get('field_type')->first()->getString() : '';
    $allowed_values = $actualNode->getFieldDefinition('field_the_language_in_which_the_')->getFieldStorageDefinition()->getSetting('allowed_values');
    $state_value = $actualNode->get('field_the_language_in_which_the_')->value;
    $language = $allowed_values[$state_value];

    return array(
      'title' => $title,
      'authors' => $authors,
      'year' => $year,
      'link' => $link,
      'doi' => $doi,
      'isbn' => $isbn,
      'abstract' => $abstract,
      'annotation' => $annotation,
      'type' => $type,
      'language' => $language
      );

  }

}
