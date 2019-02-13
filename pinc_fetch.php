<?php
error_reporting(0);

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php');

if (!is_user_logged_in() || !wp_verify_nonce($_GET['nonce'], 'ajax-nonce')) { die(); }

/**
 * Helper class to fetch pinnable content
 */
class PinFetcher {
  //
  protected $url;
  //
  protected $response;
  //
  protected $settings;
  //
  protected $data;
  //
  protected $page;
  //
  protected $parser;

  /**
   *
   */
  public function __construct() {
    $this->url = 'http' . $_GET['url'];

    $this->data['pinnable'] = false;

    if (preg_match('/\.google\./', $this->url)) {
      $this->settings['parser'] = 'parserGoogle';
    } else {
      $this->settings['parser'] = 'parser' . str_replace(' ', '', ucwords(str_replace(
        array('www.', '.', '-'),
        array('', ' ', ' '),
        parse_url($this->url)['host']
      )));
    }
  }

  /**
   * Sets the user agent for the grabber.
   */
  public function setUserAgent($user_agent) {
    $this->settings['user_agent'] = $user_agent;
  }

  /**
   * Fetches the data from the remote url
   */
  public function fetch() {
        $args = array(
      'user-agent' => $this->settings['user_agent']
    );

    switch ($this->settings['parser']) {
      case 'parserVimeoCom':
        $api_url = 'https://vimeo.com/api/oembed.json?url=';
        $this->response = wp_remote_get($api_url . $this->url, $args);
        break;
      default:
        $this->response = wp_remote_get($this->url, $args);

        if (!$this->validateResponse()) {
          return [];
        }

        $this->getContentType();
        $this->getTitle();

        break;
    }

    $parser = 'parserDefault';

    if (method_exists($this, $this->settings['parser'])) {
      $parser = $this->settings['parser'];
    }


    $this->{$parser}();

    $html = '<!DOCTYPE html>';
    $head = self::wrap('head',
      self::wrap('title', '&nbsp;') .
      '<meta charset="UTF-8" />'
    );

    $pins = '';

    if (!empty($this->data['pinnable'])) {
      foreach ($this->data['pinnable'] as $data) {
        if (!isset($data['src'])) {
          continue;
        }

        $pins .= '<a'
          . ' data-video-id="'
          . (isset($data['video_id']) ?  $data['video_id'] : '')
          . '" title="'
          . (isset($data['title']) ?  $data['title'] : '')
          . '" href="#"><img src="' . $data['src'] . '" /></a>';
      }
    }

    $body = self::wrap('body',
      self::wrap('pinctitle', $this->data['title']) .
      $pins
    );

    $html .= self::wrap('html', $head . $body);

    return $html;
  }

  /**
   * Sets the parser
   */
  public function setParser($parser) {
    $this->settings['parser'] = $parser;
  }

  /**
   * Sets the content type
   */
  protected function getContentType() {
    preg_match("/content=\"text\/(.*)>/i", $this->response['body'], $this->data['content_type']);
    $this->data['content_type'] = isset($this->data['content_type'][0]) ? $this->data['content_type'][0] : false;
  }

  /**
   * Sets the pin title from the remote page title
   */
  protected function getTitle() {
    preg_match("/<title(.*?)>(.*?)<\/title>/is", $this->response['body'], $this->data['title']);
    $this->parseTitle();
  }

  protected function parseTitle() {
    $this->data['title'] = !empty($this->data['title']) ? end($this->data['title']) : '';
    //for multiple languages in title ref: http://php.net/manual/en/function.htmlentities.php
    if (strpos($this->data['content_type'], '8859-1') !== false) {
      $this->data['title'] = htmlentities($this->data['title'], ENT_QUOTES, 'ISO-8859-1');
    } else if (strpos($this->data['content_type'], '8859-5') !== false) {
      $this->data['title'] = htmlentities($this->data['title'], ENT_QUOTES, 'ISO-8859-5');
    } else if (strpos($this->data['content_type'], '8859-15') !== false) {
      $this->data['title'] = htmlentities($this->data['title'], ENT_QUOTES, 'ISO-8859-15');
    } else if (strpos($this->data['content_type'], '866') !== false) {
      $this->data['title'] = htmlentities($this->data['title'], ENT_QUOTES, 'cp866');
    } else if (strpos($this->data['content_type'], '1251') !== false) {
      $this->data['title'] = htmlentities($this->data['title'], ENT_QUOTES, 'cp1251');
    } else if (strpos($this->data['content_type'], '1252') !== false) {
      $this->data['title'] = htmlentities($this->data['title'], ENT_QUOTES, 'cp1252');
    } else if (stripos($this->data['content_type'], 'koi8') !== false) {
      $this->data['title'] = htmlentities($this->data['title'], ENT_QUOTES, 'KOI8-R');
    } else if (stripos($this->data['content_type'], 'hkscs') !== false) {
      $this->data['title'] = mb_convert_encoding($this->data['title'], 'UTF-8', 'BIG5-HKSCS');
    } else if (stripos($this->data['content_type'], 'big5') !== false || strpos($content_type[0], '950') !== false ) {
      $this->data['title'] = mb_convert_encoding($this->data['title'], 'UTF-8', 'BIG5');
    } else if (strpos($this->data['content_type'], '2312') !== false || strpos($content_type[0], '936') !== false ) {
      $this->data['title'] = mb_convert_encoding($this->data['title'], 'UTF-8', 'GB2312');
    } else if (stripos($this->data['content_type'], 'jis') !== false || strpos($content_type[0], '932') !== false ) {
      $this->data['title'] = mb_convert_encoding($this->data['title'], 'UTF-8', 'Shift_JIS');
    } else if (stripos($this->data['content_type'], 'jp') !== false) {
      $this->data['title'] = mb_convert_encoding($this->data['title'], 'UTF-8', 'EUC-JP');
    } else {
      $this->data['title'] = htmlentities($this->data['title'], ENT_QUOTES, 'UTF-8');
    }
  }

  /**
   * Validates the response from the remote url
   */
  protected function validateResponse() {
    if (is_wp_error($this->response)) {
      $this->output('error' . $this->response->get_error_message());
      return false;
    }

    if (strpos($this->response['headers']['content-type'], 'text/') === false) {
      $this->output('error' . __('Invalid content', 'pinc'));
      return false;
    }

    if ($this->response['response']['code'] != '200') {
      $this->output('error' . __('Invalid response code', 'pinc'));
      return false;
    }

    if ($this->url == 'http://http') {
      $this->output('error' . __('Invalid url', 'pinc'));
      return false;
    }

    return true;
  }

  /**
   * Generates the output message
   */
  protected function output($message) {
    print $message;
  }

  /**
   * Creates a html element from the arguments
   */
  protected static function wrap($tag, $data = '') {
    return '<' . $tag . '>' . $data . '</' . $tag . '>';
  }

  /**
   * Default content parser
   */
  protected function parserDefault() {
    $dom = new domDocument;
    libxml_use_internal_errors(true);
    print var_export($this->response, true);
    exit;
    $dom->loadHTML($this->response['body']);
    $dom->preserveWhiteSpace = false;
    $images = $dom->getElementsByTagName('img');

    foreach ($images as $image) {
      if (empty($image->getAttribute('src'))) {
        continue;
      }

      $prepend = '';
      if (substr($image->getAttribute('src'), 0, 2) == '//') {
        $prepend = 'http:';
      }

      $this->data['pinnable'][] = array(
        'src' => $prepend . $image->getAttribute('src')
      );
    }
  }

  /**
   * Normalizes dynamic URLs
   */
  protected static function normalizeUrl($url) {
    if (strpos($url, '//') === 0) {
      return preg_replace('/^\/\//', 'https://', $url);
    }

    return $url;
  }

  /**
   * Parses open graph metas
   */
  protected function ogParser() {
    $dom = new domDocument;
    libxml_use_internal_errors(true);
    $dom->loadHTML($this->response['body']);
    $dom->preserveWhiteSpace = false;
    $images = $dom->getElementsByTagName('img');
    $xpath = new DOMXPath($dom);
    $query = '//*/meta[starts-with(@property, \'og:image\')]';
    $metas = $xpath->query($query);
    foreach ($metas as $meta) {
      $src = $meta->getAttribute('content');

      $this->data['pinnable'][] = array(
        'src' => self::normalizeUrl($src),
        'title' => $this->data['title'],
      );
    }
  }

  /**
   * Google Images
   */
  protected function parserGoogle() {
    $data = $this->response['body'];

    $dom = new domDocument;
    libxml_use_internal_errors(true);
    $dom->loadHTML($data);
    $dom->preserveWhiteSpace = false;

    $xpath = new DOMXPath($dom);
    $query = '//div[contains(@class, \'rg_meta\')]';

    $image_metas = $xpath->query($query);

    foreach ($image_metas as $image_meta) {
      $json = $image_meta->nodeValue;
      $image_info = json_decode($json);

      $this->data['pinnable'][] = array(
        'src' => $image_info->ou,
        'title' => $image_info->pt,
      );
    }
  }
  /*****
   * VIMEO
   *****/

  /**
   * Parses Vimeo single video pages
   */
  protected function parserVimeoCom() {
    $data = json_decode($this->response['body']);

    if (isset($data->thumbnail_url)) {
      $this->data['pinnable'][] = array(
        'src' => $data->thumbnail_url,
        'video_id' => $data->video_id,
        'title' => $data->title
      );
    }
  }

  /*****
   * REDTUBE
   *****/

  /**
   * Parses Redtube single video pages
   */
  protected function parserRedtubeCom() {
    $this->ogParser();
    $this->listParserRedtubeCom();
  }

  /**
   * Parses video lists of Redtube
   */
  protected function listParserRedtubeCom() {
    $dom = new domDocument;
    libxml_use_internal_errors(true);
    $dom->loadHTML($this->response['body']);
    $dom->preserveWhiteSpace = false;

    $xpath = new DOMXPath($dom);
    $query = '//a[contains(@class, \'widget-video-link\')]';

    $videos = $xpath->query($query);

    foreach ($videos as $video) {
      $cover = $video->getElementsByTagName('img')->item(0);
      $src = $cover->getAttribute('src');
      $this->data['pinnable'][] = array(
        'src' => self::normalizeUrl($src),
        'title' => $video->getAttribute('title'),
        'video_id' => trim($video->getAttribute('href'), '/'),
      );
    }

    $xpath = new DOMXPath($dom);
    $query = '//a[contains(@class, \'video-thumb\')]';

    $videos = $xpath->query($query);
    foreach ($videos as $video) {
      $cover = $video->getElementsByTagName('img')->item(0);
      $src = $cover->getAttribute('data-src');

      $this->data['pinnable'][] = array(
        'src' => self::normalizeUrl($src),
        'title' => $video->getAttribute('title'),
        'video_id' => trim($video->getAttribute('href'), '/'),
      );
    }
  }

  /*****
   * XVIDEOS
   *****/

  /**
   * Parses videos of Xvideos
   */
  protected function parserXvideosCom() {
    $this->ogParser();
    $this->listParserXvideosCom();
  }

  /**
   * Parses video lists of Xvideos
   */
  protected function listParserXvideosCom() {
    $dom = new domDocument;
    libxml_use_internal_errors(true);
    $dom->loadHTML($this->response['body']);
    $dom->preserveWhiteSpace = false;

    $xpath = new DOMXPath($dom);
    $query = '//div[contains(@class, \'thumb-block\')]';

    $videos = $xpath->query($query);

    foreach ($videos as $video) {
      $cover = $video->getElementsByTagName('img')->item(0);
      $title = $video->getElementsByTagName('p')->item(0);
      $src = $cover->getAttribute('data-src');
      $this->data['pinnable'][] = array(
        'src' => self::normalizeUrl($src),
        'title' => $title->nodeValue,
        'video_id' => $cover->getAttribute('data-videoid'),
      );
    }
  }


  /*****
   * YOUPORN
   *****/
  /**
   * Parses videos of Youporn
   */
  protected function parserYoupornCom() {
    $this->ogParser();
    $this->listParserYoupornCom();
  }

  /**
   * Parses video lists of Youporn
   */
  protected function listParserYoupornCom() {
    $dom = new domDocument;
    libxml_use_internal_errors(true);

    $dom->loadHTML($this->response['body']);
    $dom->preserveWhiteSpace = true;

    $xpath = new DOMXPath($dom);
    $query = '//a[contains(@class, \'video-box-image\')]';

    $videos = $xpath->query($query);

    foreach ($videos as $video) {
      $cover = $video->getElementsByTagName('img')->item(0);
      $video_info = explode('/', $video->getAttribute('href'));

      $src = $cover->getAttribute('data-thumbnail');
      $this->data['pinnable'][] = array(
        'src' => self::normalizeUrl($src),
        'title' => $cover->getAttribute('alt'),
        'video_id' => $video_info[2],
      );
    }
  }

  /*****
   * PORNHUB
   *****/
  /**
   * Parses videos of Pornhub
   */
  protected function parserPornhubCom() {
    $this->ogParser();
    $this->listParserPornhubCom();
  }

  /**
   * Parses video lists of Pornhub
   */
  protected function listParserPornhubCom() {
    $dom = new domDocument;
    libxml_use_internal_errors(true);

    $dom->loadHTML($this->response['body']);
    $dom->preserveWhiteSpace = true;

    $xpath = new DOMXPath($dom);
    $query = '//div[contains(@class, \'videoPreviewBg\')]';

    $videos = $xpath->query($query);

    foreach ($videos as $video) {
      $cover = $video->getElementsByTagName('img')->item(0);
      $src = $cover->getAttribute('data-image');

      if (!$src || $src == '') {
        $src = $cover->getAttribute('data-mediumthumb');
      }

      $href = $video->parentNode->getAttribute('href');
      $url_parts = explode('=', $href);
      $video_id = $url_parts[1];

      $this->data['pinnable'][] = array(
        'src' => self::normalizeUrl($src),
        'title' => $cover->getAttribute('alt'),
        'video_id' => $video_id,
      );
    }
  }

  /*****
   * XHAMSTER
   *****/
  /**
   * Parses videos of Xhamster
   */
  protected function parserXhamsterCom() {
    $dom = new domDocument;
    libxml_use_internal_errors(true);
    $dom->loadHTML($this->response['body']);
    $dom->preserveWhiteSpace = false;
    $images = $dom->getElementsByTagName('img');
    $xpath = new DOMXPath($dom);
    $query = '//*/link[contains(@itemprop, \'thumbnailUrl\')]';
    $covers = $xpath->query($query);
    foreach ($covers as $cover) {
      $this->data['pinnable'][] = array(
        'src' => $cover->getAttribute('href')
      );
    }

    $this->listParserXhamsterCom();
  }

  /**
   * Parses video lists of Xhamster
   */
  protected function listParserXhamsterCom() {
    $dom = new domDocument;
    libxml_use_internal_errors(true);
    $dom->loadHTML($this->response['body']);
    $dom->preserveWhiteSpace = false;
    $images = $dom->getElementsByTagName('img');
    $xpath = new DOMXPath($dom);
    $query = '//img[contains(@class, \'thumb-image-container__image\')]';
    $covers = $xpath->query($query);

    foreach ($covers as $cover) {
      $src = $cover->getAttribute('src');
      $url_parts = explode('/', $src);
      sscanf(end($url_parts), '%d_%d', $index, $video_id);

      $this->data['pinnable'][] = array(
        'src' => self::normalizeUrl($src),
        'title' => $cover->getAttribute('alt'),
        'video_id' => $video_id,
      );
    }
  }
}

$fetcher = new PinFetcher();
$fetcher->setUserAgent('Mozilla/5.0 (Windows NT 6.3; rv:36.0) Gecko/20100101 Firefox/36.0');

$body = $fetcher->fetch();

$body = absolute_url($body, parse_url($url, PHP_URL_SCHEME) . '://' . parse_url($url, PHP_URL_HOST));

print $body;

//convert relative to absolute path for images
//ref: http://www.howtoforge.com/forums/showthread.php?t=4
function absolute_url($txt, $base_url){
  $needles = array('src="');
  $new_txt = '';
  if(substr($base_url,-1) != '/') $base_url .= '/';
  $new_base_url = $base_url;
  $base_url_parts = parse_url($base_url);

  foreach($needles as $needle){
    while($pos = strpos($txt, $needle)){
      $pos += strlen($needle);
      if(substr($txt,$pos,7) != 'http://' && substr($txt,$pos,8) != 'https://' && substr($txt,$pos,6) != 'ftp://' && substr($txt,$pos,9) != 'mailto://'){
        if(substr($txt,$pos,1) == '/') $new_base_url = $base_url_parts['scheme'].'://'.$base_url_parts['host'];
        $new_txt .= substr($txt,0,$pos).$new_base_url;
      } else {
        $new_txt .= substr($txt,0,$pos);
      }
      $txt = substr($txt,$pos);
    }
    $txt = $new_txt.$txt;
    $new_txt = '';
  }
  return $txt;
}
?>
