function SklinetNette_Authorizator_VoterPanel() {

    /**
     * Data model
     * @type {*[]}
     */
    var voteResults = [];

    var voteResultsJSON = '';

    /**
     * Filter wrapper element
     * @type {HTMLElement}
     */
    var filter = document.getElementById('sklinet-nette-voter-filter');
    /**
     * @type {HTMLElement}
     */
    var table = document.getElementById('sklinet-nette-voter-table');

    /**
     * Filter voteResults
     *
     * @param voteResult
     * @return {boolean}
     * @private
     */
    function _filterVoteResults(voteResult) {
        if(typeof filter == 'undefined') {
            console.error('Element with id "sklinet-nette-voter-filter" does not exist!');

            return false;
        }

        var inputAttribute = filter.querySelector('input[name="attribute"]');
        var inputSubject = filter.querySelector('input[name="subject"]');
        var inputVoter = filter.querySelector('input[name="voter"]');
        var selectResult = filter.querySelector('select[name="result"]');
        /**
         * @type {boolean}
         */
        var filterResult = false;

        //if no input/select is field
        if(! inputAttribute.value && ! inputSubject.value && ! inputVoter.value && !selectResult.value) {
            return true;
        }

        //filter by result
        if(selectResult.value) {
            var result = (voteResult.result == selectResult.value ? true : false);

            if (result) {
                filterResult = true;
            } else {
                return false;
            }
        }
        //filter by attribute
        if (inputAttribute.value) {
            var result = (voteResult.attribute.indexOf(inputAttribute.value) >= 0 ? true : false);

            if (result) {
                filterResult = true;
            } else {
                return false;
            }
        }
        //filter by subject
        if (inputSubject.value) {
            var result = (voteResult.subject.indexOf(inputSubject.value) >= 0 ? true : false);

            if (result) {
                filterResult = true;
            } else {
                return false;
            }
        }
        //filter by voter
        if (inputVoter.value) {
            var result = (voteResult.voter.indexOf(inputVoter.value) >= 0 ? true : false);

            if (result) {
                filterResult = true;
            } else {
                return false;
            }
        }

        return filterResult;
    }

    /**
     * @param inputValue
     * @param objectProperty
     * @private
     * @return {boolean}
     */
    function _filterCompare(inputValue, objectProperty) {
        var filterResult = true;

        if(inputValue) {
            var result = (voteResults[objectProperty] == inputValue ? true : false);

            if (result) {
                filterResult = true;
            } else {
                filterResult = false;
            }
        }

        return filterResult;
    }

    /**
     * @return {*[]}
     */
    function getFilteredVoteResults() {
        return voteResults.filter(_filterVoteResults);
    }


    /**
     * Render filtered vote results
     */
    function renderVoteResults() {
        var html = '';
        var voteResults = getFilteredVoteResults();

        html += '<table style=\'margin-bottom: 20px; min-width: 400px; font-size: 12.5px\'>';

        voteResults.forEach(function(voteResult, index, array) {
            index = parseInt(index) + 1;

            html += ' ' +
                '     <tr class="empty-row" style=\'font-weight: 700\'>\n' +
                '         <td></td>\n' +
                '         <td></td>\n' +
                '     </tr>\n' +
                '     <tr style=\'font-weight: 700\'>\n' +
                '         <td>#'+ index +'</td> \n' +
                '         <td></td>\n' +
                '     </tr>\n' +
                '     <tr>\n' +
                '         <td><b>Attribute:</b></td> \n' +
                '         <td>'+ voteResult.attribute +'</td>\n' +
                '     </tr>\n' +
                '     <tr>\n' +
                '         <td><b>Subject:</b></td> \n' +
                '         <td>'+ voteResult.subject +'</td>\n' +
                '     </tr>\n' +
                '     <tr>\n' +
                '         <td><b>Voter:</b></td>\n' +
                '         <td>'+ voteResult.voter +'</td>\n' +
                '     </tr>\n' +
                '     <tr>\n' +
                '         <td><b>Result:</b></td> \n' +
                '         <td>'+ voteResult.voteResult +'</td>\n' +
                '     </tr>\n';
        });

        html += '</table>';

        //
        table.innerHTML = html;
    }

    /**
     *
     * @param callback
     * @param {int} ms
     * @return {function(...[*]=)}
     * @private
     */
    function _delay(callback, ms) {
        var timer = 0;

        return function() {
            var context = this, args = arguments;
            clearTimeout(timer);
            timer = setTimeout(function () {
                callback.apply(context, args);
            }, ms || 0);
        };
    }

    /**
     * Events
     */
    filter.querySelectorAll('input, select').forEach(function(input) {
        //select
        input.addEventListener('change', function(e) {
            //rerender vote results
            renderVoteResults();
        })

        //input text
        input.addEventListener('keyup', _delay(function(e) {
            //rerender vote results
            renderVoteResults();
        }, 150))
    });


    /**
     * Public interface
     */
    return {
        setVoteResults: function (data) {
            voteResults = data;
        },
        getVoteResults: function () {
            return voteResults;
        },
        render: renderVoteResults
    }
};

var SklinetNette_Authorizator_VoterPanel = SklinetNette_Authorizator_VoterPanel();



