<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Get current search params from _GET
 */
function modelcat_getsearchparams() {
  $params = array(
    "gender" => "all"
  );
  $p = $_GET;

  if( !empty( $p['gender'] )) {
    switch( $p['gender'] ) {
      case "all":
        $params["gender"] = "all";
        break;
      case "female":
        $params["gender"] = "female";
        break;
      case "male":
        $params["gender"] = "male";
        break;
      default:
        $params["gender"] = "all";
        break;
    }
  }

  return $params;
}

/**
 * Run & render search with current search params
 */
function modelcat_runsearch() {
  $params = modelcat_getsearchparams();
  $results = modelcat_ajax::getresults($params);

  $i = 0;
  foreach( $results as $r ) {
    if( $i == 0 ) {
      echo '<div class="row">';
    }
    ?>

      <div class="col-4">
        <div class="item-holder">
          <a href="<?php echo $r['permalink']; ?>">
          <div class="item-img">
            <img src="<?php echo $r['mainthumb']; ?>" class="img-fluid"/>
            <div class="overlay-holder">
              <div class="overlay">
                <ul><li>Age: <?php echo $r['info']['age']; ?></li></ul>
              </div>
            </div>
          </div>
          <div class="name"><?php echo $r['name']; ?></div></a>
          <div class="heart" data-id="<?php echo $r['id']; ?>"><i class="far fa-heart"></i></div>
        </div>
      </div>

    <?php
    if( ++$i == 3 ) {
      echo '</div>';
      $i = 0;
    }
  }

  if( $i > 0 ) {
    echo '</div>';
  }
}

?>
