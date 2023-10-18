<table id="example" class="table-auto mt-5" style="width:100%">
    <thead class="font-normal">
      <tr>
        <th class="border text-left whitespace-nowrap px-2 text-sm font-semibold text-gray-500 py-2">DARBC ID
        </th>
        <th class="border text-left whitespace-nowrap px-2 text-sm font-semibold text-gray-500 py-2">LAST NAME
        </th>
        <th class="border text-left whitespace-nowrap px-2 text-sm font-semibold text-gray-500 py-2">FIRST NAME
        </th>
        <th class="border text-left whitespace-nowrap px-2 text-sm font-semibold text-gray-500 py-2">AREA
        </th>
        <th class="border text-left whitespace-nowrap px-2 text-sm font-semibold text-gray-500 py-2">DATE ATTENDED
        </th>
        <th class="border text-left whitespace-nowrap px-2 text-sm font-semibold text-gray-500 py-2">TIME ATTENDED
        </th>
      </tr>
    </thead>
    <tbody class="">
      @foreach ($attendance as $item)
        <tr>
          <td class="border text-gray-600 text-sm whitespace-nowrap px-3 py-1">{{ strtoupper($item->member->darbc_id) }}</td>
          <td class="border text-gray-600 text-sm whitespace-nowrap px-3  py-1">{{ strtoupper($item->member->last_name) }}</td>
          <td class="border text-gray-600 text-sm whitespace-nowrap px-3  py-1">{{ strtoupper($item->member->first_name) }}</td>
          <td class="border text-gray-600 text-sm whitespace-nowrap px-3  py-1">{{ strtoupper($item->member->area) }}</td>
          <td class="border text-gray-600 text-sm whitespace-nowrap px-3  py-1">{{ strtoupper(\Carbon\Carbon::parse($item->member->created_at)->format('F d, Y')) }}</td>
          <td class="border text-gray-600 text-sm whitespace-nowrap px-3  py-1">{{ strtoupper(\Carbon\Carbon::parse($item->member->created_at)->format('h:i A')) }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
