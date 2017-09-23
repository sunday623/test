function initSchool(module, keyword) {
  var patt = new RegExp('(' + keyword + ')_([0-9]+)');
  forEach($G('datatable').elems('*'), function () {
    if (patt.test(this.id)) {
      $G(this).addEvent('change', function () {
        var hs = patt.exec(this.id);
        if (hs) {
          send('index.php/school/model/' + module + '/action', 'action=' + hs[1] + '&id=' + hs[2] + '&value=' + this.value, doFormSubmit);
        }
      });
    }
  });
}
function initSchoolImportStudent() {
  callClick('example', function () {
    var q = 'module=school-export&type=student';
    forEach($G('setup_frm').elems('select'), function () {
      q += ('&' + this.name + '=' + this.value);
    });
    this.href = WEB_URL + 'export.php?' + q;
  });
}
function initSchoolImportgrade() {
  callClick('example', function () {
    if ($E('year').value.toInt() == 0) {
      alert(trans('Please fill in') + ' ' + $E('year').title);
      $G('year').highlight().focus();
      return false;
    } else {
      var q = 'module=school-export&type=grade';
      q += '&course=' + $E('course').value;
      q += '&room=' + $E('room').value;
      q += '&year=' + $E('year').value;
      q += '&term=' + $E('term').value;
      this.href = WEB_URL + 'export.php?' + q;
    }
  });
}
function initSchoolCourse() {
  var getQuery = function () {
    return 'course_code=' + encodeURIComponent($E('course_code').value);
  };
  initAutoComplete('course_code', 'school/model/autocomplete/findCourse', 'course_code,course_name', 'elearning', {get: getQuery});
}