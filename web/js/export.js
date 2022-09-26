const LIMIT_PROCESSING_TIME = 60000;
const CHECK_INTERVAL_TIME = 1000;

const exportCsvButton = document.querySelector('.import-csv-button');
const loadingAnimation = document.querySelector('.loading-animation');
const exportError = document.querySelector('.export-error');

exportCsvButton.addEventListener('click', exportCsv);

let timerId;

function exportCsv(event) {
    setStatusProcessing();

    event.preventDefault();
    const exportUrl = event.target.getAttribute('href');

    fetch(exportUrl)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'ok') {
                waitResult(data.checkUrl)
            } else {
                setStatusError('Incorrect result');
            }
        })
        .catch(err => {
            setStatusError('Response error')
        });
}

function waitResult(checkUrl) {
    timerId = setInterval(checkExportStatus, CHECK_INTERVAL_TIME, [checkUrl]);
    setTimeout(() => {
        clearInterval(timerId);
        setStatusError('Runtime error')
    }, LIMIT_PROCESSING_TIME);
}

function checkExportStatus(checkUrl) {
    fetch(checkUrl)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'ok') {
                clearInterval(timerId);
                setStatusCompleted(data.path);
            }
        })
        .catch(err => {
            clearInterval(timerId);
            setStatusError('Response error');
        });
}


function setStatusProcessing() {
    exportCsvButton.classList.add('disabled');
    exportCsvButton.setAttribute('disabled', '');
    loadingAnimation.classList.remove('hide');
    exportError.classList.add('hide');
}

function setStatusError(error) {
    exportCsvButton.removeAttribute('disabled');
    exportCsvButton.classList.remove('disabled');
    loadingAnimation.classList.add('hide');
    exportError.classList.remove('hide');
    exportError.textContent = error;
}

function setStatusCompleted(path) {
    exportCsvButton.removeAttribute('disabled');
    exportCsvButton.classList.remove('disabled');
    loadingAnimation.classList.add('hide');
    window.location.href = path;
}