function showServiceModal(serviceName) {
    document.getElementById('modalServiceName').textContent = serviceName;
    var myModal = new bootstrap.Modal(document.getElementById('serviceModal'));
    myModal.show();
}
