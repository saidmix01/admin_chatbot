<!-- Modal -->
<div class="modal fade" id="modal_new_question" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">New question</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="new_question_form">
          <input type="hidden" name="que_parent" id="que_parent" value="0">
            <div class="form-group">
                <label for="que_order">Order Question</label>
                <input type="number" name="que_order" id="que_order" class="form-control" value="1">
            </div>
            <div class="form-group">
                <label for="que_question">Write your question</label>
                <textarea class="form-control" id="que_question" name="que_question" rows="3"></textarea>
            </div>
            <div class="form-group">
                <button class="btn btn-success" onclick="save_question()">Save</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>