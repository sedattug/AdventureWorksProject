/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$(document).ready(function () {

    $(function () {
        $('#review').EnsureMaxLength({
            limit: 3000,
            cssClass: 'char-len-span, font-weight-light'
        });
    });

});

