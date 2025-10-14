<form action="{{ route('test-wa.send') }}" method="POST">
    @csrf
    <label>Phone</label>
    <input type="text" name="phone" placeholder="628xxx..." required>
    <label>Message</label>
    <textarea name="message" required>Kode OTP 123456</textarea>
    <button type="submit">Kirim</button>
</form>

@if(session('message'))
    <div>{{ session('message') }}</div>
@endif
