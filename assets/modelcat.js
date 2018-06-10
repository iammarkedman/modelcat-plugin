/* Nano Templates - https://github.com/trix/nano */

function nano(template, data) {
  return template.replace(/\{([\w\.]*)\}/g, function(str, key) {
    var keys = key.split("."), v = data[keys.shift()];
    for (var i = 0, l = keys.length; i < l; i++) v = v[keys[i]];
    return (typeof v !== "undefined" && v !== null) ? v : "";
  });
}

var searchResult = '<div class="col-4"><img src="{mainthumb}" class="img-fluid"/>{name}</div>';

var modelcatSearchDefaultSettings = {
  form: "#searchForm"
};

/**
 * Modelcat
 */
(function($) {

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
          $root.empty();

          $i = 0;
          $html = "";
          $.each( response, function(idx, data) {
            if ($i === 0) {
              $html += '<div class="row">';
            }
            $html += nano(searchResult, data);
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
        })
        .fail(function(response) {
          console.log("FAIL", response);
        });
    });
  };

})(jQuery);

