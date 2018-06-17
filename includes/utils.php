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

      <div class="col-md-4 result-item" data-id="<?php echo $r['id']; ?>">
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
          <div class="name float-left"><?php echo $r['name']; ?></div></a>
          <div class="corner"></div>
        </div>
        <div class="float-right"><a class="select" href="#"><i class="fa fa-thumbtack"></i></a></div>
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

/**
 * Display selected models
 */
function modelcat_selected( $ids ) {
  $params = array(
    "ids" => $ids
  );

  $results = modelcat_ajax::getresults($params);
  foreach( $results as $r ): ?>

    <div class="row selected-model" data-id="<?php echo $r['id']; ?>">
      <div class="col-2">
        <div class="item-img">
          <a href="<?php echo $r['permalink']; ?>">
            <img src="<?php echo $r['mainthumb']; ?>" class="img-fluid"/>
          </a>
          <div class="corner"></div>
        </div>
      </div>
      <div class="col-10">
        <a href="<?php echo $r['permalink']; ?>">
          <h2><?php echo $r['name']; ?></h2>
          <p><?php echo $r['info']['age']; ?> years old</p>
        </a>
        <a class="remove" data-id="<?php echo $r['id']; ?>" href="#"><i class="fa fa-times-circle"></i> Remove</a>
      </div>
    </div>

  <?php endforeach; 
}

?>
