<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Weekly Tag report</title>
</head>
<body>
<div>
  <h2>Weekly search tag report </h2>
  <h3>Application name : {{ $app_name }} </h3>
  <p>This is search tag report of last 20 success & last 20 fail.</p>

  <table border=1 style="display: table;border-collapse: separate;border-color: grey;width: 100%;color: #212529;">
    <thead>
    <tr style="padding:.50rem;vertical-align: middle;border-bottom-width: 2px;border-top-width: 2px;">
      <th>No</th>
      <th>Search query</th>
      <th>Search count</th>
      <th>Content count</th>
      <th>Sub category name</th>
      <th>Status</th>
    </tr>
    </thead>
    <tbody>
    @if(!empty($message_body))
      @foreach($message_body AS $i => $row)
        <tr style="border: 1px solid black;">
          <td style="padding:.50rem;vertical-align: middle;text-align: center;">{{  $i + 1 }}</td>
          <td style="padding:.50rem;vertical-align: middle;text-align: center;">{{ isset($row->tag) ? $row->tag : '' }}</td>
          <td style="padding:.50rem;vertical-align: middle;text-align: center;">{{ isset($row->search_count) ? $row->search_count : '' }}</td>
          <td style="padding:.50rem;vertical-align: middle;text-align: center;">{{ isset($row->content_count) ? $row->content_count :'' }}</td>
          <td style="padding:.50rem;vertical-align: middle;text-align: center;">{{ isset($row->sub_category_name) && $row->sub_category_name !='' ? $row->sub_category_name :'-' }}</td>
          @if(isset($row->is_success) && $row->is_success == 1)
            <td style="padding:.50rem;vertical-align: middle;text-align: center;color: green !important;">Success</td>
          @else
            <td style="padding:.50rem;vertical-align: middle;text-align: center;color: #dc3545!important;">Fail</td>
          @endif
        </tr>
      @endforeach
    @else
      <tr style="border: 1px solid black;">
        <td colspan="7" style="padding:.50rem;vertical-align: middle;text-align: center;">No search tag found.</td>
      </tr>
    @endif
    </tbody>
  </table>


</div>
</body>
</html>
