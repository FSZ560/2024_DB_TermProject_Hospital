$(document).ready(function() {
    // 預設
    $('th').each(function() {
        $(this).addClass('descending');  // 預設為降冪
    });
    
    // 自動顯示第一列的 down 圖示
    var firstColumn = $('th').eq(0); // 第一列
    if (firstColumn.index() !== $('th').length - 1) {  // 忽略最後一欄
        firstColumn.addClass('descending'); // 設置為降冪排序
        firstColumn.append('<span class="sort-icon"><i class="bx bx-sort-down"></i></span>'); // 顯示圖示
    }
    firstColumn.append('<span class="sort-icon"><i class="bx bx-sort-down"></i></span>'); // 顯示圖示

    // 點擊排序功能
    firstColumn.trigger('click'); // 模擬第一次點擊排序

    $('th').click(function() {
        var index = $(this).index();

        
        var rows = $('tbody tr').toArray();
        var isAscending = $(this).hasClass('descending');
        
        // 先移除所有表頭的圖示
        $('th').find('.sort-icon').remove();

        // 根據排序方向添加相應的圖示
        if (index !== $('th').length - 1) {
            var icon = isAscending ? '<i class="bx bx-sort-down"></i>' : '<i class="bx bx-sort-up"></i>';
            $(this).append('<span class="sort-icon">' + icon + '</span>'); // 在當前點擊的表頭中顯示排序圖示
        }
        // 排序表格
        rows.sort(function(a, b) {
            var cellA = $(a).children('td').eq(index).text();
            var cellB = $(b).children('td').eq(index).text();
            if ($.isNumeric(cellA) && $.isNumeric(cellB)) {
                return cellA - cellB;
            }
            return cellA.localeCompare(cellB);
        });

        // 如果是升冪，反轉行數據
        if (isAscending) {
            rows.reverse();
        }

        // 移除所有的 ascending 和 descending 樣式
        $('th').removeClass('ascending descending');

        // 設置當前列的排序方向樣式
        if (isAscending) {
            $(this).addClass('ascending');
        } else {
            $(this).addClass('descending');
        }

        // 更新表格內容
        $('tbody').append(rows);
    });
});