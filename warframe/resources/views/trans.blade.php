<!DOCTYPE html>
<html>
<head>
    <title>Translate</title>
</head>
<body>
<ul>
@foreach($items as $item)
    <li>
        <label class="string required" for="user_nickname">Time: {{ $item->created_at }}</label>
        <label class="string required" for="user_nickname">{{ $item->item }} => {{ $item->trans }}</label>
        <form method="POST" action="/updateTrans">
            <div style="display:none">
                <input type="hidden" name="_id" value="{{ $item->id }}">
            </div>
            <input aria-required="true" class="string required form-control input-small" name="trans" required="required" type="text">
            <button name="submit" value="profile" class="btn submit">提交</button>
        </form>
    </li>
@endforeach
</ul>
</body>
</html>