<?php

namespace Drupal\enrope_user_match\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\StreamWrapper\PrivateStream;
use Drupal\Core\StreamWrapper\PublicStream;
use Drupal\Core\Url;

class KeywordsForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'batch_keywords_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['#prefix'] = t('This is users keywords export');

    $form['actions'] = [
      '#type' => 'actions',
      'submit' => [
        '#type' => 'submit',
        '#value' => 'Proceed',
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $query = db_select('users', 'u');
    $query->addField('u', 'uid');
    $query->orderBy('u.uid');
    $uids = $query->execute()->fetchCol();

    foreach ($uids as $uid) {
      $operations[] = [
        '\Drupal\enrope_user_match\Form\KeywordsForm::users_keywords_batch_process',
        [$uid],
      ];
    }


    $batch['operations'] = $operations;
    $batch += [
      'title' => 'Collecting keywords',
      'init_message' => 'Starting process',
      'error_message' => 'An unrecoverable error has occurred. You can find the error message below. It is advised to copy it to the clipboard for reference.',
      'finished' => '\Drupal\enrope_user_match\Form\KeywordsForm::users_keywords_batch_finished',
    ];

    batch_set($batch);

    $form_state->setRebuild(TRUE);
  }

  public function users_keywords_batch_process($uid, &$sandbox) {


    $item = user_load($uid);

    $field_research_topic = $item->get('field_research_topic')->value;
    $field_research_interests_fields = $item->get('field_research_interests_fields')->value;
    $field_strengths = $item->get('field_strengths')->value;
    $all = "$field_research_topic $field_research_interests_fields $field_strengths";
    $keywords = mb_strtolower($all);

    // Stop words
    $commonWords = array('a','able','about','above','abroad','according','accordingly','across','actually','adj','after','afterwards','again','against','ago','ahead','ain\'t','all','allow','allows','almost','alone','along','alongside','already','also','although','always','am','amid','amidst','among','amongst','an','and','another','any','anybody','anyhow','anyone','anything','anyway','anyways','anywhere','apart','appear','appreciate','appropriate','are','aren\'t','around','as','a\'s','aside','ask','asking','associated','at','available','away','awfully','b','back','backward','backwards','be','became','because','become','becomes','becoming','been','before','beforehand','begin','behind','being','believe','below','beside','besides','best','better','between','beyond','both','brief','but','by','c','came','can','cannot','cant','can\'t','caption','cause','causes','certain','certainly','changes','clearly','c\'mon','co','co.','com','come','comes','concerning','consequently','consider','considering','contain','containing','contains','corresponding','could','couldn\'t','course','c\'s','currently','d','dare','daren\'t','definitely','described','despite','did','didn\'t','different','directly','do','does','doesn\'t','doing','done','don\'t','down','downwards','during','e','each','edu','eg','eight','eighty','either','else','elsewhere','end','ending','enough','entirely','especially','et','etc','even','ever','evermore','every','everybody','everyone','everything','everywhere','ex','exactly','example','except','f','fairly','far','farther','few','fewer','fifth','first','five','followed','following','follows','for','forever','former','formerly','forth','forward','found','four','from','further','furthermore','g','get','gets','getting','given','gives','go','goes','going','gone','got','gotten','greetings','h','had','hadn\'t','half','happens','hardly','has','hasn\'t','have','haven\'t','having','he','he\'d','he\'ll','hello','help','hence','her','here','hereafter','hereby','herein','here\'s','hereupon','hers','herself','he\'s','hi','him','himself','his','hither','hopefully','how','howbeit','however','hundred','i','i\'d','ie','if','ignored','i\'ll','i\'m','immediate','in','inasmuch','inc','inc.','indeed','indicate','indicated','indicates','inner','inside','insofar','instead','into','inward','is','isn\'t','it','it\'d','it\'ll','its','it\'s','itself','i\'ve','j','just','k','keep','keeps','kept','know','known','knows','l','last','lately','later','latter','latterly','least','less','lest','let','let\'s','like','liked','likely','likewise','little','look','looking','looks','low','lower','ltd','m','made','mainly','make','makes','many','may','maybe','mayn\'t','me','mean','meantime','meanwhile','merely','might','mightn\'t','mine','minus','miss','more','moreover','most','mostly','mr','mrs','much','must','mustn\'t','my','myself','n','name','namely','nd','near','nearly','necessary','need','needn\'t','needs','neither','never','neverf','neverless','nevertheless','new','next','nine','ninety','no','nobody','non','none','nonetheless','noone','no-one','nor','normally','not','nothing','notwithstanding','novel','now','nowhere','o','obviously','of','off','often','oh','ok','okay','old','on','once','one','ones','one\'s','only','onto','opposite','or','other','others','otherwise','ought','oughtn\'t','our','ours','ourselves','out','outside','over','overall','own','p','particular','particularly','past','per','perhaps','placed','please','plus','possible','presumably','probably','provided','provides','q','que','quite','qv','r','rather','rd','re','really','reasonably','recent','recently','regarding','regardless','regards','relatively','respectively','right','round','s','said','same','saw','say','saying','says','second','secondly','see','seeing','seem','seemed','seeming','seems','seen','self','selves','sensible','sent','serious','seriously','seven','several','shall','shan\'t','she','she\'d','she\'ll','she\'s','should','shouldn\'t','since','six','so','some','somebody','someday','somehow','someone','something','sometime','sometimes','somewhat','somewhere','soon','sorry','specified','specify','specifying','still','sub','such','sup','sure','t','take','taken','taking','tell','tends','th','than','thank','thanks','thanx','that','that\'ll','thats','that\'s','that\'ve','the','their','theirs','them','themselves','then','thence','there','thereafter','thereby','there\'d','therefore','therein','there\'ll','there\'re','theres','there\'s','thereupon','there\'ve','these','they','they\'d','they\'ll','they\'re','they\'ve','thing','things','think','third','thirty','this','thorough','thoroughly','those','though','three','through','throughout','thru','thus','till','to','together','too','took','toward','towards','tried','tries','truly','try','trying','t\'s','twice','two','u','un','under','underneath','undoing','unfortunately','unless','unlike','unlikely','until','unto','up','upon','upwards','us','use','used','useful','uses','using','usually','v','value','various','versus','very','via','viz','vs','w','want','wants','was','wasn\'t','way','we','we\'d','welcome','well','we\'ll','went','were','we\'re','weren\'t','we\'ve','what','whatever','what\'ll','what\'s','what\'ve','when','whence','whenever','where','whereafter','whereas','whereby','wherein','where\'s','whereupon','wherever','whether','which','whichever','while','whilst','whither','who','who\'d','whoever','whole','who\'ll','whom','whomever','who\'s','whose','why','will','willing','wish','with','within','without','wonder','won\'t','would','wouldn\'t','x','y','yes','yet','you','you\'d','you\'ll','your','you\'re','yours','yourself','yourselves','you\'ve','z','zero');

    $input = preg_replace('/\b('.implode('|',$commonWords).')\b/','',$keywords);
    $input = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", " ", $input)));


    $words = str_word_count($input, 1);

    $sandbox['results']['keywords'][] = $words;


  }


  /**
   * Batch 'finished' callback
   */
  public static function users_keywords_batch_finished($success, $results, $operations) {

    $messenger = \Drupal::messenger();

    if ($success) {
      $private_path = PrivateStream::basepath();
      $public_path = PublicStream::basepath();
      $file_base = ($private_path) ? $private_path : $public_path;
      $name = 'enrope_users_keywords';
      $filename = $name . '.txt';
      $filepath = $file_base . '/' . $filename;
      $csvFile = fopen($filepath, "w");


      foreach ($results['keywords'] as $keyword) {
        fwrite($csvFile, implode( ", ", $keyword ));
      }

      fclose($csvFile);

      header('Content-Type: application/x-research-info-systems');
      header('Content-Disposition: attachment; filename="' . basename($filepath) . '";');
      header('Content-Length: ' . filesize($filepath));


      $pass_link = \Drupal::l(t('right click and Save as'), Url::fromUri(file_create_url($filepath)));

      $messenger
        ->addMessage(t('Keywords ready, @link', ['@link' => $pass_link]));

    }
    else {
      $error_operation = reset($operations);
      $messenger
        ->addMessage(t('An error occurred while processing @operation with arguments : @args', [
          '@operation' => $error_operation[0],
          '@args' => print_r($error_operation[0], TRUE),
        ]));
    }
  }


}
