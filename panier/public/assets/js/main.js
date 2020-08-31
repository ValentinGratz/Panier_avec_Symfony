$( document ).ready(function() {
    let ligneUp = $('.up');
    let ligneDown = $('.down');

    ligneUp.on('click', function(){
        let that = $(this);

        let idProduct = that.parent().parent().find('.idPanier').data('id');

        let qty = that.parent('td').find('.quantite');

        let tva = that.parent().parent().find('.tva').text();

        $.ajax({
            url: "/card/ajaxaddquantity/" + idProduct,
            method: "POST",
            dataType: "json",
            data: { id: idProduct},
            success: function(data){

                let t = that.parent().parent().find('.prixTTC').text(data.prix * data.quantite * (1 + (tva/100))).text();

                if(data.info){
                    /*
                    mise à jour des quantités de la ligne
                     */
                    qty.text(data.quantite);
                    /*
                    mise à jour du total de la ligne
                     */
                    that.parent().parent().find('.prixTTC').text(parseFloat(t).toFixed(2));

                    /*
                    mise à jour des quantité totale
                     */
                    let totalQuantite = $(".nbArticle").text();
                    $(".nbArticle").text(parseFloat(totalQuantite) + 1);

                    /*
                    mise à jour du total general
                     */
                    let totalTTCAllLignes = $('.prixTTC');

                    let tg = 0;
                    for(let i = 0; i < totalTTCAllLignes.length; i++){
                        let ele = totalTTCAllLignes[i].innerText;
                        tg += parseFloat(ele);

                    }

                    $('.totalGeneral1, .totalGeneral').text(tg);

                    /*
                    mise à jour du total HT
                     */
                    let totalHtContent =  $('.totalGeneral1').text()

                    let totalHT = parseFloat(totalHtContent/(1 + tva/100)).toFixed(2);

                    $('.totalHT').text(totalHT);

                    /*
                    mise à jour de la valeur de la tva
                     */

                    // contenue du champs TVA
                    let tvaContent = $('.valTVA').text();

                    let valTvaTotal =  parseFloat(totalHT * (tva/100)).toFixed(2);

                    // modif de la valeur de la TVA
                    $('.valTVA').text(valTvaTotal);

                }
            },
            error: function(){
                console.log("erreur requete ajax");
            }
        })
    })

    ligneDown.on('click', function(){
        let that = $(this);

        let idProduct = that.parent().parent().find('.idPanier').data('id');

        let qty = that.parent('td').find('.quantite');

        let tva = that.parent().parent().find('.tva').text();
        let totalQuantite = $(".quantite").text();

        $.ajax({
            url: "/card/ajax-supp-quantity/" + idProduct,
            method: "POST",
            dataType: "json",
            data: { id: idProduct},
            success: function(data){
                //console.log(data);
                let t = that.parent().parent().find('.prixTTC').text(data.prix * data.quantitesupp * (1 + (tva/100))).text();

                if(data.info){
                    /*
                    mise à jour des quantités de la ligne
                     */
                    qty.text(data.quantitesupp);
                    /*
                    mise à jour du total de la ligne
                     */
                    that.parent().parent().find('.prixTTC').text(parseFloat(t).toFixed(2));

                    /*
                    mise à jour des quantité totale
                     */
                    let totalQuantite = $(".nbArticle").text();
                    $(".nbArticle").text(parseFloat(totalQuantite) - 1);

                    /*
                     mise à jour du total general
                     */
                    let totalTTCAllLignes = $('.prixTTC');

                    let tg = 0;
                    for(let i = 0; i < totalTTCAllLignes.length; i++){
                        let ele = totalTTCAllLignes[i].innerText;
                        tg += parseFloat(ele);

                    }

                    $('.totalGeneral1, .totalGeneral').text(tg);

                    /*
                   mise à jour du total HT
                    */
                    let totalHtContent =  $('.totalGeneral1').text()

                    let totalHT = parseFloat(tg/(1 + tva/100)).toFixed(2);

                    $('.totalHT').text(totalHT);

                    /*
                    mise à jour de la valeur de la tva
                     */

                    // contenue du champs TVA
                    let tvaContent = $('.valTVA').text();

                    let valTvaTotal =  parseFloat(totalHT * (tva/100)).toFixed(2);

                    // modif de la valeur de la TVA
                    $('.valTVA').text(valTvaTotal);


                }},
            error: function(){
                console.log("erreur requete ajax");
            }
        })
    })

    let clearfix = $('.clearfix');

    clearfix.on('mouseenter', function(){

        $(this).addClass('clearfix-more');
        $(this).removeClass('clearfix');
    })

    clearfix.on('mouseleave', function(){

        $(this).addClass('clearfix');
        $(this).removeClass('clearfix-more');

    })


});