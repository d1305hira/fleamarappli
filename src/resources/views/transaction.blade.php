@extends('layouts.app')

@section('css')
<link href="{{ asset('css/transaction.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container py-4">
  <div class="left_block">
  <label class="mb-4">その他の取引</label>

  @foreach($otherItems as $other)
    <div class="other-item mb-2">
      <a href="{{ route('transaction.show', $other->id) }}">
        {{ $other->name }}
      </a>
    </div>
  @endforeach

  @if($otherItems->isEmpty())
    <p class="text-muted">進行中の取引はありません</p>
  @endif
</div>

  <div class="right_block">
    <div class="block1">
      <h1 class="mb-4"> {{ optional($item->purchase->user)->name }}さんとの取引画面 </h1>
      @if ($item->purchase && auth()->id() === $item->purchase->user_id)
        <button class="complete-button btn btn-success mb-4">取引を完了する</button>
      @endif
    </div>
    <div class="block2">
      <div class="item-image">
        <a href="{{ route('item.show', $item->id) }}">
          <img src="{{ Storage::url($item->image) }}" class="card-img-top" alt="{{ $item->name }}">
        </a>
        <h5>商品画像</h5>
      </div>
      <div class="item-info">
        <p class="card-text">商品名 {{ $item->name }}</p>
        <p class="card-text">商品価格 ¥{{ number_format($item->price) }}</p>
      </div>
    </div>
    <div class="block3">
      <div class="chat">
        @foreach($messages as $message)
        <div class="chat-message {{ $message->user_id === auth()->id() ? 'my-message' : 'other-message' }}">

          {{-- メッセージ本文 --}}
          <div>
            <strong>{{ $message->user->name }}:</strong>
            {{ $message->message }}
            @if($message->image)
              <div class="mt-2">
                <img src="{{ Storage::url($message->image) }}" class="img-fluid" alt="Message Image">
              </div>
            @endif
          </div>

          {{-- 日付 --}}
          <div class="text-muted small">
            {{ $message->created_at->format('Y-m-d H:i') }}
          </div>

          {{-- 右下の編集・削除ボタン（自分のメッセージのみ） --}}
          @if($message->user_id === auth()->id())
          <div class="message-actions text-end mt-1">
            <button class="edit-button btn btn-sm btn-outline-secondary"
                  data-message-id="{{ $message->id }}"
                  data-message-text="{{ $message->message }}">編集
            </button>
            <form action="{{ route('messages.destroy', $message->id) }}"
                method="POST"
                style="display:inline;">
              @csrf
              @method('DELETE')
              <button class="btn btn-sm btn-outline-danger">削除</button>
            </form>
          </div>
          @endif
        </div>
        @endforeach
      </div>
      <!-- 編集フォーム（非表示） -->
      <div id="editForm" class="edit-form" style="display:none; margin-top: 20px;">
        <form id="editMessageForm" method="POST">
      @csrf
      @method('PATCH')
        <textarea name="message" id="editMessageText" class="form-control" rows="2"></textarea>
          <div class="mt-2 d-flex gap-2">
            <button class="btn btn-primary btn-sm">更新</button>
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeEditForm()">キャンセル</button>
          </div>
        </form>
      </div>

      {{-- バリデーションエラー表示 --}}
    @if ($errors->any())
    <div class="alert alert-danger mb-3" style="color:red;">
        @foreach ($errors->all() as $error)
            <p class="mb-1">{{ $error }}</p>
        @endforeach
    </div>
    @endif


      <form id="chat-form" action="{{ route('transaction.message', $item->id) }}" method="POST" enctype="multipart/form-data">
      @csrf
      <textarea id="chatInput" name="message" class="form-control mb-2" rows="2" placeholder="メッセージを入力"></textarea>
      <input type="file" name="image" class="form-control mb-2">
      <button class="btn btn-primary">送信</button>
      </form>

    </div>
    <!-- 取引完了モーダル -->
    <div id="completeModal" class="modal-overlay" style="display:none;">
  <div class="modal-content">
    <h3>取引が完了しました。</h3>
    <p>今回の取引相手はどうでしたか？</p>

    <div class="stars">
      <span data-value="1">★</span>
      <span data-value="2">★</span>
      <span data-value="3">★</span>
      <span data-value="4">★</span>
      <span data-value="5">★</span>
    </div>

    <form id="ratingForm">
      @csrf
      <input type="hidden" name="rating" id="ratingValue">
      <input type="hidden" name="transaction_id" value="{{ $item->id }}">
      <button id="sendRating" type="submit" class="btn btn-primary mt-3">送信する</button>
    </form>
  </div>
</div>

    @php
    $shouldOpenModal =
      auth()->id() === $item->user_id &&   // 出品者で
      $item->buyer_completed &&            // 購入者が評価済み
      !$item->seller_completed;            // 出品者はまだ
    @endphp

    @if ($shouldOpenModal)
    <script>
      document.addEventListener('DOMContentLoaded', function() {
      document.getElementById('completeModal').style.display = 'flex';
      });
    </script>
    @endif
  </div>
</div>
@endsection

@section('scripts')
<script>
const myId = {{ auth()->id() }};

// ===============================
// リアルタイム受信（Echo）
// ===============================
//Echo.private("chat.{{ $item->id }}")
  //  .listen("MessageSent", (e) => {
    //    console.log("受信:", e);
//
  //      const chat = document.querySelector('.chat');
//
  //      chat.insertAdjacentHTML('beforeend', `
    //        <div class="chat-message ${e.message.user_id === myId ? 'my-message' : 'other-message'}">
      //          <div>
        //            <strong>${e.message.user.name}:</strong>
          //          ${e.message.message}
            //    </div>
              //  <div class="text-muted small">${e.message.created_at}</div>
            //</div>
    //    `);

      //  chat.scrollTop = chat.scrollHeight;
  //  });


// ===============================
// DOMContentLoaded 内で全イベント登録
// ===============================
document.addEventListener('DOMContentLoaded', function() {

  // -------------------------------
  // 取引完了モーダル
  // -------------------------------
  const completeBtn = document.querySelector('.complete-button');
  const modal = document.getElementById('completeModal');
  const stars = document.querySelectorAll('.stars span');
  let selectedRating = 0;

  // 購入者側は complete-button を押したときだけ開く
  if (completeBtn) {
    completeBtn.addEventListener('click', function() {
        modal.style.display = 'flex';
    });
  }

  stars.forEach(star => {
  star.addEventListener('click', function() {
    selectedRating = this.dataset.value;

    // ★ これを追加
    document.getElementById('ratingValue').value = selectedRating;

    stars.forEach(s => s.classList.remove('selected'));
    for (let i = 0; i < selectedRating; i++) {
      stars[i].classList.add('selected');
    }
  });
});


  const sendRatingBtn = document.getElementById('sendRating');
if (sendRatingBtn) {
  sendRatingBtn.addEventListener('click', function(e) {
  e.preventDefault();

  if (selectedRating == 0) {
    alert("星を選択してください");
    return;
  }

  const formData = new FormData();
  formData.append('rating', selectedRating);
  formData.append('_token', "{{ csrf_token() }}");

  fetch("{{ route('transaction.complete', $item->id) }}", {
    method: "POST",
    body: formData
  })
  .then(res => res.json())
  .then(() => {
    alert("評価を送信しました！");
    window.location.href = "/";
  })
  .catch(err => console.error("送信エラー:", err));
});
}


  // -------------------------------
  // 編集ボタン
  // -------------------------------
  document.querySelectorAll('.edit-button').forEach(btn => {
  btn.addEventListener('click', () => {

    const id = btn.dataset.messageId;
    const text = btn.dataset.messageText;

    const messageDiv = btn.closest('.chat-message');
    const rect = messageDiv.getBoundingClientRect();

    const editForm = document.getElementById('editForm');

    editForm.style.display = 'block';
    editForm.style.top = (window.scrollY + rect.bottom + 5) + 'px';
    editForm.style.left = (rect.left) + 'px';

    document.getElementById('editMessageText').value = text;
    document.getElementById('editMessageForm').action = '/messages/' + id;
  });
});


}); // DOMContentLoaded 終了

// ===============================
// 編集フォームを閉じる
// ===============================
function closeEditForm() {
  document.getElementById('editForm').style.display = 'none';
}

// ===============================
// 入力途中のメッセージを保持
// ===============================
const chatInput = document.getElementById('chatInput');

// ページ読み込み時に復元
const draftKey = 'draft_message_' + {{ $item->id }};
const savedDraft = localStorage.getItem(draftKey);
if (savedDraft) {
    chatInput.value = savedDraft;
}

// 入力するたびに保存
chatInput.addEventListener('input', () => {
    localStorage.setItem(draftKey, chatInput.value);
});

// 送信したら削除
document.getElementById('chat-form').addEventListener('submit', () => {
    localStorage.removeItem(draftKey);
});


</script>
@endsection



