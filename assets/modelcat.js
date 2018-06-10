/* Nano Templates - https://github.com/trix/nano */

function nano(template, data) {
  return template.replace(/\{([\w\.]*)\}/g, function(str, key) {
    var keys = key.split("."), v = data[keys.shift()];
    for (var i = 0, l = keys.length; i < l; i++) v = v[keys[i]];
    return (typeof v !== "undefined" && v !== null) ? v : "";
  });
}

var ntmpl_searchResult = '<div class="col-4"><a href="{permalink}"><img src="{mainthumb}" class="img-fluid"/><div class="name">{name}</div></a></div>';

var modelcatSearchDefaultSettings = {
  form: "#searchForm"
};

/**
 * Modelcat
 */
(function($) {

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
    });
  }

  /**
   * Run search
   */
  $.fn.modelcatSearch = function(options) {
    options = $.extend({}, modelcatSearchDefaultSettings, options || {});
    var $form = $(options.form);

    return this.each(function() {
      var $root = $(this);

      var datastring = "action=getresults&" + $form.serialize();
      $.ajax({
        url: modelcat.ajax_url,
        method: "POST",
        data: datastring
      })
        .done(function(response) {
          // save to localStorage as last results
          localStorage.setItem("res", JSON.stringify(response));

          // render
          $root.renderSearchResults( response );
        })
        .fail(function(response) {
          console.log("FAIL", response);
        });
    });
  };

})(jQuery);

