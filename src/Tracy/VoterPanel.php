<?php declare(strict_types = 1);

namespace SklinetNette\Authorizator\Tracy;

use Nette\Security\User;
use Tracy\IBarPanel;

/**
 * Class VoterPanel
 *
 * @package SklinetNette\Authorizator\Tracy
 */
class VoterPanel implements IBarPanel
{
    private static $voteResults = [];

    /**
     * {@inheritDoc}
     * @return string
     */
    public function getTab()
    {
        $html = '
        <span title="Authorization aplikace (Voter)">
	        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 1000 1000" enable-background="new 0 0 1000 1000" xml:space="preserve">
                <g><path d="M947.2,339.4L529.7,755c-10.5,10.4-27.5,10.4-38,0l-8.4-8.4h0l-9.1-9.1l-39.4-39.2c-0.3-0.3-0.3-0.6-0.6-0.9l-180.6-181c-10.5-10.4-10.5-27.4,0-37.8l56.9-56.7c10.5-10.4,27.5-10.4,38,0l162.4,162.8L852.3,245c10.5-10.4,27.5-10.4,38,0l57,56.6C957.7,312.1,957.7,329,947.2,339.4z M706.1,212.1c-38.9-35.5-43.9-100.5-43.9-100.5s-96.7-17.3-195-17.3c-96.6,0-195,17.3-195,17.3s-6.6,65.3-43.5,99c-39.1,35.8-108.7,40-108.7,40s-7.5,127.1,3.2,237.6c10.5,108.7,37.4,216.2,137.1,297.6C341.9,852.4,464,905.7,464,905.7s95.9-41.9,165.3-87.3c76.3-50.1,148.1-118.6,170-248.8c0.9-5.2,1.6-10.6,2.4-15.9l79.8-79.5c-3,37.3-7.2,74.8-13.1,109.9c-26.5,157.3-113.2,240-205.3,300.5C579.2,939.5,463.4,990,463.4,990s-147.4-64.2-246-144.7C97,747,64.5,617.2,51.8,485.8C39,352.4,48,198.8,48,198.8s84.1-5.1,131.3-48.2c44.6-40.7,52.5-119.5,52.5-119.5S350.6,10,467.4,10c118.8,0,235.5,21.1,235.5,21.1s6,78.4,53,121.2c20.4,18.6,49.1,29.8,74.6,36.6l-58.7,54.5C749.8,237.9,724,228.4,706.1,212.1z"/></g>
            </svg>
	        <span class="tracy-label">Authorization - Voter ('. count(static::$voteResults) .' calls)</span>
        </span>';

        return $html;
    }

    /**
     * {@inheritDoc}
     * @return string
     */
    public function getPanel()
    {
        $countVoteResults = count(static::$voteResults);

        if($countVoteResults < 1) {
            return '';
        }

        $html = '
        <h1>Authorization - Voter</h1>
        <div class="tracy-inner">
            <div class="tracy-inner-container">
                <!-- styles -->
                ' . static::getCssHtml() .'
                <!-- filter -->
                <p><b>Filter:</b></p>
                <div id="sklinet-nette-voter-filter">
                    <input type="text" autocomplete="off" placeholder="Attribute" name="attribute">
                    <input type="text" autocomplete="off" placeholder="Subject" name="subject">
                    <input type="text" autocomplete="off" placeholder="Voter" name="voter">
                    <select name="result">
                        <option value="">--Result--</option>
                        <option value="1">Granted</option>
                        <option value="0">Abstain</option>
                        <option value="-1">Deny</option>
                    </select>
                </div>
                <div id="sklinet-nette-voter-table">
                </div>
                <!-- filter JS -->
                ' . static::getJsHtml();

        $html .= '
                <script>
                    var voteResultsJSON = '. json_encode(static::$voteResults) .';
                    /* Fill data */
                    SklinetNette_Authorizator_VoterPanel.setVoteResults(voteResultsJSON);
                    /* Render */
                    SklinetNette_Authorizator_VoterPanel.render();
                </script>
            </div>
        </div>';

        return $html;
    }

    public static function addVoteResult($attribute, $subject, $calledVoterClass, int $voteResult)
    {
        static::$voteResults[] = [
            'attribute'        => $attribute,
            'subject'          => static::convertToString($subject) . ":" . static::getSubjectId($subject),
            'voter'            => static::convertToString($calledVoterClass),
            'voteResult'       => $voteResult . " (<b>" . static::voteTextResult($voteResult) . "</b>)",
            'result'           => $voteResult
        ];
    }

    /**
     * @param $item
     *
     * @return string
     */
    private static function convertToString($item): string
    {
        if(
            ( !is_array( $item ) ) &&
            ( ( !is_object( $item ) && settype( $item, 'string' ) !== false ) ||
              ( is_object( $item ) && method_exists( $item, '__toString' ) ) )
        ) {
            return (string) $item;
        } elseif(is_object($item)) {
            return get_class($item);
        }

        return sprintf('Item "%s" could not be converted to string', $item);
    }

    private static function voteTextResult($voteResult) {
        if($voteResult == '1') {
            return '<span style="color:green">Granted</span>';
        } elseif ($voteResult == '0') {
            return '<span style="color:orange">Abstain</span>';
        } else {
            return '<span style="color:red">Deny</span>';
        }
    }

    /**
     * @param $subject
     *
     * @return int|string||null
     */
    private static function getSubjectId($subject) {
        if(is_object( $subject ) && method_exists( $subject, 'getId' ) ) {
            return $subject->getId();
        }

        return null;
    }

    /**
     * @return string
     */
    private static function getCssHtml()
    {
        $html = '<style>';
        $html .= file_get_contents(__DIR__ . './../assets/main.css');
        $html .= '</style>';

        return $html;
    }

    /**
     * @return string
     */
    private static function getJsHtml()
    {
        $html = '<script>';
        $html .= file_get_contents(__DIR__ . './../assets/main.js');
        $html .= '</script>';

        return $html;
    }
}
