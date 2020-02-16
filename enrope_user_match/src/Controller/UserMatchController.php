<?php


namespace Drupal\enrope_user_match\Controller;


use Drupal\Core\Controller\ControllerBase;

class UserMatchController extends ControllerBase {

  /**
   * Display the markup.
   *
   * @return array
   *   Return markup array.
   */
  public function content() {

    $user = \Drupal::routeMatch()->getRawParameter('user');

    if(!empty($user)){

      $user_account = \Drupal\user\Entity\User::load($user); // pass your uid

      $field_research_topic = $user_account->get('field_research_topic')->value;
      $field_research_interests_fields = $user_account->get('field_research_interests_fields')->value;
      $field_strengths = $user_account->get('field_strengths')->value;

      $all = "$field_research_topic $field_research_interests_fields $field_strengths";

      if(!empty($all)){

        $ids = $this->getMatchedProfiles($all, $user);

      }

    }

    if(!empty($ids)){
      $matched_users = user_load_multiple($ids);

      return [
        '#type' => 'markup',
        '#markup' => render(user_view_multiple($matched_users, 'enrope_user_in_members_list')),
        '#prefix' => '<div class="alert alert-primary" role="alert">
                      '.t("This is a list of other members of ENROPE that have similar interests in their profiles").'
                    </div><div class="view-content row">',
        '#suffix' => '</div>',
      ];
    }else{
      return [
        '#type' => 'markup',
        '#markup' => t('No matches found'),
      ];
    }


  }

  private function removeCommonWords($input){

    // Stop words
    $commonWords = array('a','able','about','above','abroad','according','accordingly','across','actually','adj','after','afterwards','again','against','ago','ahead','ain\'t','all','allow','allows','almost','alone','along','alongside','already','also','although','always','am','amid','amidst','among','amongst','an','and','another','any','anybody','anyhow','anyone','anything','anyway','anyways','anywhere','apart','appear','appreciate','appropriate','are','aren\'t','around','as','a\'s','aside','ask','asking','associated','at','available','away','awfully','b','back','backward','backwards','be','became','because','become','becomes','becoming','been','before','beforehand','begin','behind','being','believe','below','beside','besides','best','better','between','beyond','both','brief','but','by','c','came','can','cannot','cant','can\'t','caption','cause','causes','certain','certainly','changes','clearly','c\'mon','co','co.','com','come','comes','concerning','consequently','consider','considering','contain','containing','contains','corresponding','could','couldn\'t','course','c\'s','currently','d','dare','daren\'t','definitely','described','despite','did','didn\'t','different','directly','do','does','doesn\'t','doing','done','don\'t','down','downwards','during','e','each','edu','eg','eight','eighty','either','else','elsewhere','end','ending','enough','entirely','especially','et','etc','even','ever','evermore','every','everybody','everyone','everything','everywhere','ex','exactly','example','except','f','fairly','far','farther','few','fewer','fifth','first','five','followed','following','follows','for','forever','former','formerly','forth','forward','found','four','from','further','furthermore','g','get','gets','getting','given','gives','go','goes','going','gone','got','gotten','greetings','h','had','hadn\'t','half','happens','hardly','has','hasn\'t','have','haven\'t','having','he','he\'d','he\'ll','hello','help','hence','her','here','hereafter','hereby','herein','here\'s','hereupon','hers','herself','he\'s','hi','him','himself','his','hither','hopefully','how','howbeit','however','hundred','i','i\'d','ie','if','ignored','i\'ll','i\'m','immediate','in','inasmuch','inc','inc.','indeed','indicate','indicated','indicates','inner','inside','insofar','instead','into','inward','is','isn\'t','it','it\'d','it\'ll','its','it\'s','itself','i\'ve','j','just','k','keep','keeps','kept','know','known','knows','l','last','lately','later','latter','latterly','least','less','lest','let','let\'s','like','liked','likely','likewise','little','look','looking','looks','low','lower','ltd','m','made','mainly','make','makes','many','may','maybe','mayn\'t','me','mean','meantime','meanwhile','merely','might','mightn\'t','mine','minus','miss','more','moreover','most','mostly','mr','mrs','much','must','mustn\'t','my','myself','n','name','namely','nd','near','nearly','necessary','need','needn\'t','needs','neither','never','neverf','neverless','nevertheless','new','next','nine','ninety','no','nobody','non','none','nonetheless','noone','no-one','nor','normally','not','nothing','notwithstanding','novel','now','nowhere','o','obviously','of','off','often','oh','ok','okay','old','on','once','one','ones','one\'s','only','onto','opposite','or','other','others','otherwise','ought','oughtn\'t','our','ours','ourselves','out','outside','over','overall','own','p','particular','particularly','past','per','perhaps','placed','please','plus','possible','presumably','probably','provided','provides','q','que','quite','qv','r','rather','rd','re','really','reasonably','recent','recently','regarding','regardless','regards','relatively','respectively','right','round','s','said','same','saw','say','saying','says','second','secondly','see','seeing','seem','seemed','seeming','seems','seen','self','selves','sensible','sent','serious','seriously','seven','several','shall','shan\'t','she','she\'d','she\'ll','she\'s','should','shouldn\'t','since','six','so','some','somebody','someday','somehow','someone','something','sometime','sometimes','somewhat','somewhere','soon','sorry','specified','specify','specifying','still','sub','such','sup','sure','t','take','taken','taking','tell','tends','th','than','thank','thanks','thanx','that','that\'ll','thats','that\'s','that\'ve','the','their','theirs','them','themselves','then','thence','there','thereafter','thereby','there\'d','therefore','therein','there\'ll','there\'re','theres','there\'s','thereupon','there\'ve','these','they','they\'d','they\'ll','they\'re','they\'ve','thing','things','think','third','thirty','this','thorough','thoroughly','those','though','three','through','throughout','thru','thus','till','to','together','too','took','toward','towards','tried','tries','truly','try','trying','t\'s','twice','two','u','un','under','underneath','undoing','unfortunately','unless','unlike','unlikely','until','unto','up','upon','upwards','us','use','used','useful','uses','using','usually','v','value','various','versus','very','via','viz','vs','w','want','wants','was','wasn\'t','way','we','we\'d','welcome','well','we\'ll','went','were','we\'re','weren\'t','we\'ve','what','whatever','what\'ll','what\'s','what\'ve','when','whence','whenever','where','whereafter','whereas','whereby','wherein','where\'s','whereupon','wherever','whether','which','whichever','while','whilst','whither','who','who\'d','whoever','whole','who\'ll','whom','whomever','who\'s','whose','why','will','willing','wish','with','within','without','wonder','won\'t','would','wouldn\'t','x','y','yes','yet','you','you\'d','you\'ll','your','you\'re','yours','yourself','yourselves','you\'ve','z','zero');

    $input = preg_replace('/\b('.implode('|',$commonWords).')\b/','',$input);
    $input = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", " ", $input)));

    return $input;

  }

  private function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

  private function getMatchedProfiles($keywords, $current_user) {
    $index = \Drupal\search_api\Entity\Index::load('users');
    $query = $index->query();

    // Change the parse mode for the search.
    $parse_mode = \Drupal::service('plugin.manager.search_api.parse_mode')
      ->createInstance('direct');
    $parse_mode->setConjunction('OR');
    $query->setParseMode($parse_mode);

    $keywords = mb_strtolower($keywords);
    $keywords = $this->removeCommonWords($keywords);
    // Set fulltext search keywords and fields.

    $words = str_word_count($keywords, 1);

    $keys = array_unique(array_merge($words));


    $query->keys($keys);
    //$query->setFulltextFields(['title', 'name', 'body']);

    // Set additional conditions.
    //    $query->addCondition('status', 1)
    //      ->addCondition('author', 1, '<>');

    // Add more complex conditions.
    // (In this case, a condition for a specific datasource).
    //    $time = \Drupal::service('datetime.time')->getRequestTime();
    //    $conditions = $query->createConditionGroup('OR');
    //    $conditions->addCondition('search_api_datasource', 'entity:node', '<>')
    //      ->addCondition('created', $time - 7 * 24 * 3600, '>=');
    //    $query->addConditionGroup($conditions);
    //
    //    // Restrict the search to specific languages.
    //    $query->setLanguages(['de', 'it']);

    // Do paging.
    //$query->range(20, 10);

    // Add sorting.
    $query->sort('search_api_relevance', 'DESC');

    // Set additional options.
    // (In this case, retrieve facets, if supported by the backend.)
    //    $server = $index->getServerInstance();
    //    if ($server->supportsFeature('search_api_facets')) {
    //      $query->setOption('search_api_facets', [
    //        'type' => [
    //          'field' => 'type',
    //          'limit' => 20,
    //          'operator' => 'AND',
    //          'min_count' => 1,
    //          'missing' => TRUE,
    //        ],
    //      ]);
    //    }

    // Set one or more tags for the query.
    // @see hook_search_api_query_TAG_alter()
    // @see hook_search_api_results_TAG_alter()
    //$query->addTag('custom_search');

    // Execute the search.
    $results = $query->execute();



    $results = $results->getResultItems();


    $results_array = array();

    foreach ($results as $key => $item){

      if($item->getScore() > 1){
        array_push($results_array, $item->getId());
      }
    }


    $matched_users = array();

    $result = array_values($results_array);

    if(!empty($result)){

      foreach ($result as $item){
        array_push($matched_users, $this->get_string_between($item, '/', ':'));
      }
    }

    if (($key = array_search($current_user, $matched_users)) !== false) {
      unset($matched_users[$key]);
    }

    $matched_users = array_slice($matched_users, 0, 5);

    return $matched_users;

    //error_log(print_r($results->getResultItems(), true));
    //echo "Returned IDs: $ids.\n";
    //    $facets = $results->getExtraData('search_api_facets', []);
    //    echo 'Facets data: ' . var_export($facets, TRUE);
  }

}
