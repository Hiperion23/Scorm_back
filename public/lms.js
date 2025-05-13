function postSync(url, data = {}) {
  const xhr = new XMLHttpRequest();
  xhr.open('POST', url, false);
  xhr.setRequestHeader('Content-Type', 'application/json');
  xhr.send(JSON.stringify(data));
  return xhr.responseText;
}

window.API = {
  LMSInitialize: function () {
    return postSync('/api/scorm/initialize');
  },
  LMSFinish: function () {
    return postSync('/api/scorm/finish');
  },
  LMSGetValue: function (element) {
    return postSync('/api/scorm/get-value', { element });
  },
  LMSSetValue: function (element, value) {
    return postSync('/api/scorm/set-value', { element, value });
  },
  LMSCommit: function () {
    return postSync('/api/scorm/commit');
  },
  LMSGetLastError: function () {
    return postSync('/api/scorm/get-last-error');
  },
  LMSGetErrorString: function (code) {
    return postSync('/api/scorm/get-error-string', { code });
  },
  LMSGetDiagnostic: function (code) {
    return postSync('/api/scorm/get-diagnostic', { code });
  }
};

console.log('[SCORM] window.API cargado correctamente ');
