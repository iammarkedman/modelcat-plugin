/* Nano Templates - https://github.com/trix/nano */

function nano(template, data) {
  return template.replace(/\{([\w\.]*)\}/g, function(str, key) {
    var keys = key.split("."), v = data[keys.shift()];
    for (var i = 0, l = keys.length; i < l; i++) v = v[keys[i]];
    return (typeof v !== "undefined" && v !== null) ? v : "";
  });
}

var ntmpl_searchResult = 
  '<div class="col-4"><div class="item-holder"><a href="{permalink}">' +
    '<div class="item-img">' +
    '<img src="{mainthumb}" class="img-fluid"/>' + 
    '<div class="overlay-holder"><div class="overlay">' +
    '<ul><li>Age: {info.age}</li></ul>' +
    '</div></div>' +
    '</div>' +
    '<div class="name">{name}</div></a>' +
    '<div class="heart" data-id="{id}"><i class="far fa-heart"></i></div>' +
  '</div></div>';

var modelcatSearchDefaultSettings = {
  gender: "all"
};

var modelcatInitDefaultSettings = {
  results: "#results",
  form: "#searchForm"
};

var modelcatUpdateSearchDefaultSettings = {
  results: "#results",
  form: "#searchForm"
};


/**
 * Modelcat
 */
(function($) {

  /**
   * Update timestamp for last taken action.
   */
  $.updateLastActionTime = function() {
    localStorage.setItem("favtch", Date.now());
  }

  /**
   * Bind add/remote from favorites
   */
  $.fn.bindFavorite = function() {
    return this.each(function() {
      var $root = $(this);

      $root.click(function() {
        var favs = JSON.parse(localStorage.getItem("fav"));
        var id = $root.data("id");
        var idx = favs.indexOf(id);
        if( idx != -1 ) {
          favs.splice(idx, 1);
          localStorage.setItem("fav", JSON.stringify(favs));
          $.updateLastActionTime();
          $root.find("i").removeClass("fas").addClass("far");
        } else {
          favs.push(id);
          localStorage.setItem("fav", JSON.stringify(favs));
          $.updateLastActionTime();
          $root.find("i").removeClass("far").addClass("fas");
        }
      });
    });
  }

  /**
   * Bind add/remote from favorites for a single model
   */
  $.fn.bindSingleFavorite = function() {
    return this.each(function() {
      var $root = $(this);

      var favs = JSON.parse(localStorage.getItem("fav"));
      var id = $root.data("id");
      var idx = favs.indexOf(id);
      var inFavs = ( idx != -1 );

      if( inFavs ) {
        $root.html('<i class="fas fa-heart"></i> Remove from favorites');
      } else {
        $root.html('<i class="far fa-heart"></i> Add to favorites');
      }

      $root.click(function() {
        var favs = JSON.parse(localStorage.getItem("fav"));
        var id = $root.data("id");
        var idx = favs.indexOf(id);
        var inFavs = ( idx != -1 );

        if( inFavs ) {
          favs.splice(idx, 1);
          localStorage.setItem("fav", JSON.stringify(favs));
          $.updateLastActionTime();
          $root.html('<i class="far fa-heart"></i> Add to favorites');
        } else {
          favs.push(id);
          localStorage.setItem("fav", JSON.stringify(favs));
          $.updateLastActionTime();
          $root.html('<i class="fas fa-heart"></i> Remove from favorites');
        }
      });
    });
  }

  /**
   * Render search results
   */
  $.fn.renderSearchResults = function(results) {
    return this.each(function() {
      var $root = $(this);

      $root.empty();

      $i = 0;
      $html = "";
      $.each( results, function(idx, data) {
        if ($i === 0) {
          $html += '<div class="row">';
        }
        $html += nano(ntmpl_searchResult, data);
        if ($i === 2) {
          $html += '</div>';
          $root.append($html);
          $html = "";
        }
        if( ++$i === 3) {
          $i = 0;
        }
      });

      if ($i > 0) {
        $html += '</div>';
        $root.append($html);
      }

      // check favorite states
      var favs = JSON.parse(localStorage.getItem("fav"));
      $root.find(".heart").each( function() {
        var id = $(this).data("id");
        if( favs.includes(id) ) {
          $(this).find("i").removeClass("far").addClass("fas");
        }
      });

      // bind hearts
      $root.find(".heart").bindFavorite();
    });
  }

  /**
   * Run search
   */
  $.fn.modelcatSearch = function(options) {
    options = $.extend({}, modelcatSearchDefaultSettings, options || {});
    var opt = [];
    $.each(options, function(idx, val) {
      opt.push(idx + "=" + val);
    });
    var optionsStr = opt.join("&");

    return this.each(function() {
      var $root = $(this);

      var datastring = "action=getresults&" + optionsStr;
      $.ajax({
        url: modelcat.ajax_url,
        method: "POST",
        data: datastring
      })
        .done(function(response) {
          $.updateLastActionTime();

          // render
          $root.renderSearchResults( response );
        })
        .fail(function(response) {
          console.log("FAIL", response);
        });
    });
  };

  /**
   * Update search params with current selections
   */
  $.fn.modelcatUpdateSearchParams = function( options ) {
    options = $.extend({}, modelcatUpdateSearchDefaultSettings, options || {});
 
    return this.each(function() {
      var $root = $(this);
      var searchOpts = {};

      // gender
      var $form = $(options.form);
      $form.find("input.searchGender:checked").each( function() {
        searchOpts[$(this).data("query")] = $(this).data("val");
      });

      $(options.results).modelcatSearch(searchOpts);
    });
  }
 
  /**
   * Init search form
   */
  $.fn.modelcatInitSearchForm = function( options ) {
    options = $.extend({}, modelcatInitDefaultSettings, options || {});
 
    return this.each(function() {
      var $root = $(this);

      // gender
      $root.find("input.searchGender").change( function(e) {
        var $this = $(this);
        var chk = $this.prop("checked");
        $root.find("input.searchGender").each( function() {
          if( $(this).attr("id") !== $this.attr("id") ) {
            $(this).prop("checked", false);
          }
        });
        if( !chk ) {
          $root.find("input#g_all").prop("checked", true);
        }

        // update search params
        $root.modelcatUpdateSearchParams({
          results: options.results,
          form: "#" + $root.attr("id")
        });
      });
    });
  }

})(jQuery);

