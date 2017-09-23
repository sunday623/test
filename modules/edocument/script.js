function initEdocumentView(id) {
  callClick(id, function (e) {
    if (confirm(trans('Downloading is a signed document'))) {
      send(WEB_URL + 'index.php/edocument/model/download', 'id=' + id, doFormSubmit, this);
    }
  });
}