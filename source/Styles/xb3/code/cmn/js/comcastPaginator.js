/*
 * If not stated otherwise in this file or this component's Licenses.txt file the
 * following copyright and licenses apply:
 *
 * Copyright 2018 RDK Management
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
*/
(function ($) {
    $.fn.extend({
        comcastPaginator: function (options) {
            var settings = $.extend({
                entriesInPage: 20,
                theme: 'blue'
            }, options);
            return this.each(function () {
                var currentPage = 1;
                var entriesPerPage = settings.entriesInPage;
                var selectablePages = 10;
                var $table = $(this);
                var numOfRows = $table.find('tbody tr').length;
                var numOfPages = Math.ceil(numOfRows / entriesPerPage);
                function rePaginate(){
                    $table.find('tbody tr').hide().slice((currentPage - 1) * entriesPerPage, currentPage * entriesPerPage).show();
                    //selec page button >> 4+current+6
                    if(currentPage < 5) {start = 1; end = selectablePages;}
                    else if(currentPage+6 > numOfPages) {start = numOfPages-selectablePages+1; end = numOfPages;}
                    else {start = currentPage-4; end = currentPage+5;}
                    for (var page = 1; page <= numOfPages; page++) {
                        if(start <= page && page <= end) { $('#pageNumber_'+page).show(); }
                        else { $('#pageNumber_'+page).hide(); }
                    }
                    //hide-show buttons
                    $('#cp_first, #cp_prev, #cp_next, #cp_last').show();
                    if(currentPage == 1) { $('#cp_prev').hide(); }
                    if(currentPage < 6) { $('#cp_first').hide(); }
                    if(currentPage > (numOfPages-6)) { $('#cp_last').hide(); }
                    if(currentPage == numOfPages) { $('#cp_next').hide(); }
                    //show active page number
                    $('[id^="pageNumber_"] > a').removeClass("active");
                    $('#pageNumber_'+currentPage+' > a').addClass('active');
                    //show active page entrie number shown
                    var pageStartRow = ((currentPage - 1)*entriesPerPage) + 1;
                    var pageEndRow = currentPage*entriesPerPage;
                    pageEndRow = (pageEndRow > numOfRows)?numOfRows:pageEndRow;
                    $('.pager:visible > #cp_entries').html('<b>'+pageStartRow+'</b> - <b>'+pageEndRow+'</b> of <b>'+numOfRows+'</b>');
                    //update page number in cp_inPage
                    $('#cp_inPage').val(currentPage);
                }
                $('#comcastPaginator').remove();
                var $paginated = $('<div id="comcastPaginator" class="pager '+settings.theme+'"></div>');
                var $pager = $('<ul></ul>').css('width', '270px');
                for (var page = 1; page <= numOfPages; page++) {
                  $('<li id="pageNumber_'+page+'"><a class="'+settings.theme+' normal" href="javascript:void(0)" >'+page+'</a></li>').bind('click', {
                    newPage: page
                  }, function(event) {
                    currentPage = event.data['newPage'];
                    rePaginate();
                  }).appendTo($pager);
                }
                var btnPrev = $('<input/>').attr('id', 'cp_prev').attr('type', 'button').val('PREV').addClass('btn').click(function () { if ($(this).is(":hidden")) return false; else { currentPage = currentPage - 1;rePaginate();} });
                var btnNext = $('<input/>').attr('id', 'cp_next').attr('type', 'button').val('NEXT').addClass('btn').click(function () { if ($(this).is(":hidden")) return false; else { currentPage = currentPage + 1;rePaginate();} });
                var btnFirst = $('<input/>').attr('id', 'cp_first').attr('type', 'button').val('FIRST').addClass('btn').click(function () { if ($(this).is(":hidden")) return false; else { currentPage = 1;rePaginate();} });
                var btnLast = $('<input/>').attr('id', 'cp_last').attr('type', 'button').val('LAST').addClass('btn').click(function () { if ($(this).is(":hidden")) return false; else { currentPage = numOfPages;rePaginate();} });
                var spanEntries = $('<span/>').attr('id', 'cp_entries').css('padding-left', '15px');
                var inputPage = $('<input/>').attr('id', 'cp_inPage').attr('type', 'text');
                var btnGo = $('<input/>').attr('id', 'cp_goPage').attr('type', 'button').val('GO').addClass('btn')
                    .click(function () {
                        var goToPage = parseInt($('#cp_inPage').val());
                        if (goToPage > 0 && goToPage <= numOfPages) { currentPage = goToPage;rePaginate(); }
                        else return false;
                    });
                $paginated.insertAfter($table);
                var btnDiv = $('<div/>').addClass('short').html(inputPage.add(btnGo));
                $('.pager:visible').prepend(btnPrev).prepend(btnFirst).append($pager).append(btnNext).append(btnLast).append(spanEntries).append(btnDiv);
                rePaginate();
            });
        }
    });
})(jQuery);
