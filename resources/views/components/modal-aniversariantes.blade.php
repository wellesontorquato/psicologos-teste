<!-- resources/views/components/modal-aniversariantes.blade.php -->
<div class="modal fade" id="modalAniversariantes" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content shadow border-0">
      <div class="modal-header" style="background-color: #00aaff; color: #fff;">
        <h5 class="modal-title"><i class="bi bi-gift-fill me-2"></i>Aniversariantes de Hoje</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <ul id="lista-aniversariantes" class="list-group list-group-flush">
          <!-- preenchido via JS -->
        </ul>
      </div>
    </div>
  </div>
</div>
