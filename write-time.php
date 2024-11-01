<?php
/*
Plugin Name: article write time
Plugin URI: http://11neko.com/
Description: To display the time it took to write the article .
Version: 0.3.6
Author: shinji yonetsu
Author URI: http://11neko.com/
License: GPLv2
*/
add_action( 'plugins_loaded', 'write_time_load_textdomain' );
function write_time_load_textdomain() {
    load_plugin_textdomain( 'write-time', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');
}

function get_article_created_time() {
    date_default_timezone_set( 'Asia/Tokyo' );
    $create_time = time();
    global $post;
    $create_time_id = $post->ID;
    add_post_meta($create_time_id, 'create_time', $create_time);
}
add_action( 'admin_head-post-new.php', 'get_article_created_time' );


function get_article_edit_start_time( $post_id ){
if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
    return $post_id;

    date_default_timezone_set( 'Asia/Tokyo' );
    $start_edit_time = time();
    global $post;
    $created_edit_time_id = $post->ID;
    $post_time = $post->post_date;
    $last_edit_time = $post->post_modified;
    $post_time = strtotime($post_time);
    $last_edit_time = strtotime($last_edit_time);
    $created_total_time = get_post_meta($created_edit_time_id, 'created_total_time', false);
    $post_status = get_post_status($created_edit_time_id);

    if( $post_time === $last_edit_time or $post_status == "future"){
        $start_edit_time_flg = get_post_meta($created_edit_time_id, 'start_edit_time', false);
        if( empty($start_edit_time_flg) ){
            add_post_meta($created_edit_time_id, 'start_edit_time', $start_edit_time);
        } else{
            update_post_meta($created_edit_time_id, 'start_edit_time', $start_edit_time);
        }
        $first_created_endtime = get_post_meta( $created_edit_time_id , 'first_created_endtime' , false );
        $create_time = get_post_meta( $created_edit_time_id , 'create_time' , false );
        
        $first_created_time_anser = $first_created_endtime[0] - $create_time[0];
        $first_article_time_flg = get_post_meta($created_edit_time_id, 'total_article_time', false);
        if( empty($first_article_time_flg)){
            add_post_meta($created_edit_time_id, 'total_article_time', $first_created_time_anser);
        } else{
            update_post_meta($created_edit_time_id, 'total_article_time', $first_created_time_anser);
        }
    } else{
        $ctmf = get_post_meta($created_edit_time_id, 'start_edit_time', false);
        if( empty( $ctmf ) ){
            add_post_meta($created_edit_time_id, 'start_edit_time', $start_edit_time);
        } else{
            delete_post_meta($created_edit_time_id, 'start_edit_time');
            add_post_meta($created_edit_time_id, 'start_edit_time', $start_edit_time);
        }
    }

    if( empty($created_total_time)){
        $total_edit_time = get_post_meta($created_edit_time_id, 'total_edit_time', false);
        $total_article_time = get_post_meta($created_edit_time_id, 'total_article_time', false);
        if(!isset($total_edit_time[0])){
            $total_edit_time[0] = 0;
        }
        if(!isset($total_article_time[0])){
            $total_article_time[0] = 0;
        }
        $created_total_time = $total_edit_time[0] + $total_article_time[0];
        add_post_meta($created_edit_time_id, 'created_total_time', $created_total_time);
    } else{
        $total_edit_time = get_post_meta($created_edit_time_id, 'total_edit_time', false);
        $total_article_time = get_post_meta($created_edit_time_id, 'total_article_time', false);

        if(!isset($total_edit_time[0])){
            $total_edit_time[0] = 0;
        }
        if(!isset($total_article_time[0])){
            $total_article_time[0] = 0;
        }
        if($total_article_time[0] > 10000000){
            $created_total_time = $total_edit_time[0];
            update_post_meta($created_edit_time_id, 'created_total_time', $created_total_time);
        }
        else{
            $created_total_time = $total_edit_time[0] + $total_article_time[0];
            update_post_meta($created_edit_time_id, 'created_total_time', $created_total_time);
        }
    }

}
add_action( 'admin_head-post.php', 'get_article_edit_start_time' );

function get_article_last_edit_time($post_id){
if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
    return $post_id;
    global $post;
    $created_edit_time_id = $post->ID;
    $last_edit_time = time();
    $start_edit_time = get_post_meta($created_edit_time_id, 'start_edit_time', false);
    $first_created_endtime = get_post_meta($created_edit_time_id, 'first_created_endtime', false);

    if( !empty($first_created_endtime)){
        if( !empty($start_edit_time)){
            $start_edit_time = get_post_meta($created_edit_time_id, 'start_edit_time', false);
            $total_edit_time = get_post_meta($created_edit_time_id, 'total_edit_time', false);
            if( empty($total_edit_time)){
                $total_edit_time = $last_edit_time - $start_edit_time[0];
                add_post_meta($created_edit_time_id ,'total_edit_time', $total_edit_time);
            } else{
                $old_time = get_post_meta($created_edit_time_id, 'total_edit_time', true);
                $total_edit_time = $last_edit_time - $start_edit_time[0];
                $total_edit_time_plus = $total_edit_time + $old_time;
                update_post_meta($post_id, 'total_edit_time', $total_edit_time_plus);
            }
        }
    } else{
        $first_created_endtime = $last_edit_time;
        add_post_meta($post_id, 'first_created_endtime', $last_edit_time);
    }
}

add_action('pre_post_update', 'get_article_last_edit_time');

// Program for beginners function
// single or page 
function display_time($time_format){
    date_default_timezone_set( 'Asia/Tokyo' );
    global $post;
    $create_time = get_post_meta( $post->ID , 'create_time' , false );
    $created_time = get_post_meta( $post->ID , 'created_total_time' , true );
    $show_hor_time = floor($created_time / 3600);
    $show_min_time = floor($created_time / 60);
    $show_min_time = $show_min_time % 60;
    $show_sec_time = $created_time % 60;

    // time_format_setting
    if( isset($create_time[0]) ){
        if( !empty($created_time) or !empty($edit_time) ){
            if($time_format == 'defalut'){ 
                echo _e('This article' , 'write-time');
                if( $created_time > 60 && $created_time < 3600){
                    echo $show_min_time, _e('minute' , 'write-time'),$show_sec_time,_e('second' , 'write-time');
                } elseif( $created_time > 3600){
                    echo $show_hor_time,_e('hour' , 'write-time'),$show_min_time,_e('minute' , 'write-time'),$show_sec_time,_e('second' , 'write-time');
                } else{
                    echo $show_sec_time,_e('second' , 'write-time');
                }
                echo _e('I wrote in .' , 'write-time');
            } elseif($time_format == 'custom'){
                if( $created_time > 60 && $created_time < 3600){
                    echo $show_min_time,_e('minute' , 'write-time'),$show_sec_time,_e('second' , 'write-time');
                } elseif( $created_time > 3600 ){
                    echo $show_hor_time,_e('<span>hour</span>' , 'write-time'),$show_min_time,_e('minute' , 'write-time'),$show_sec_time,_e('second' , 'write-time');
                } else{
                    echo $show_sec_time,_e('second' , 'write-time');
                }
                
            }
        }
    }
}


// add menu style & script
add_action( 'admin_init', 'my_plugin_admin_init' );

function my_plugin_admin_init() {
    wp_register_style( 'myPluginStylesheet', plugins_url('css/style.css', __FILE__) );
    wp_register_style( 'myPluginStylesheet2', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css' );
    wp_register_script( 'my-plugin-script', plugins_url( 'js/highstock.js', __FILE__ ) );
    wp_register_script( 'my-plugin-script2', plugins_url( 'js/exporting.js', __FILE__ ) );
    wp_register_script( 'my-plugin-script3', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js');
}


// add menu

function my_plugin_admin_styles() {
    wp_enqueue_style( 'myPluginStylesheet' );
    wp_enqueue_style( 'myPluginStylesheet2' );
}

function my_plugin_admin_scripts() {
        wp_enqueue_script( 'my-plugin-script' );
        wp_enqueue_script( 'my-plugin-script2' );
        wp_enqueue_script( 'my-plugin-script3' );
    }
// display page
/////////////////////////////////////////////////////////////////

add_action('admin_menu' , 'add_hoge');
function add_hoge(){
    $page = add_menu_page('write time', 'write time', 'manage_options', 'myplugin_id', 'write_time_page','dashicons-clock');
    add_action( 'admin_print_styles-' . $page, 'my_plugin_admin_styles' );
    add_action('admin_print_scripts-' . $page, 'my_plugin_admin_scripts2');
}
function my_plugin_admin_scripts2() {
        wp_enqueue_script( 'my-plugin-script' );
        wp_enqueue_script( 'my-plugin-script2' );
        wp_enqueue_script( 'my-plugin-script3' );
    }

add_action( 'wp_ajax_'.myplugin_get_action_name(), 'myplugin_response' );
add_action( 'wp_ajax_nopriv_'.myplugin_get_action_name(), 'myplugin_response' );

function myplugin_get_action_name() {
    return 'myplugin_action';
}

function myplugin_get_nonce_name() {
    return 'myplugin-nonce';
}

function write_time_page() {
    echo '<h2>write time Page</h2>';
    echo '<div id="container"></div>';
    $nonce_name = myplugin_get_nonce_name();
    $nonce = wp_create_nonce( $nonce_name );
    $action_name = myplugin_get_action_name();
    $url = admin_url( 'admin-ajax.php' );

    $json_options = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;
    $url = json_encode( $url,  $json_options );


    $json = json_encode(
    array(
      'action' => $action_name,
      'security' => $nonce,
      'foo' => 'bar'
    ), $json_options );

    $count;
    $all_create_time;

    $wp_query = new WP_Query();
    $args = array(
            'meta_key' => 'created_total_time',
            'posts_per_page' => -1
        );
    $wp_query->query($args);


    // glaph
    echo '<div id="container"></div>';
    
    // display date
    echo '<div class="table_style">';
    echo _e('<table class="write_date"><tbody class="scrollBody"><tr><th>ID</th><th>date</th><th>title</th><th>status</th><th>write time</th><th>edit</th></tr>' , 'write-time');
    $total_count = 0;
    $count = 0;
    while ($wp_query->have_posts()) : $wp_query->the_post();
        $post_id = get_the_ID();
        $total = get_post_meta($post_id , 'created_total_time' ,true);
        $total_edit_time = get_post_meta($post_id, 'total_edit_time', false);
        $total_article_time = get_post_meta($post_id, 'total_article_time', false);

        if($total_article_time[0] > 10000000){
            if(!isset($total_edit_time[0])){
                $created_total_time = 0;
                update_post_meta($post_id, 'created_total_time', $created_total_time);
            }else{
                $created_total_time = $total_edit_time[0];
                update_post_meta($post_id, 'created_total_time', $created_total_time);
            }
        } else{
            $created_total_time = $total_edit_time[0] + $total_article_time[0];
            update_post_meta($created_edit_time_id, 'created_total_time', $created_total_time);
        }


        if(!empty($total)){
            echo '<tr><td>' , get_the_id() , '</td>';
            echo '<td>' , the_time('Y-m-d') , '</td>';
            echo '<td>' , get_the_title() , '</td>';
            echo '<td>' , get_post_status() , '</td>';
            echo '<td>';
            $show_hor_time = floor($total / 3600);
            $show_min_time = floor($total / 60);
            $show_min_time = $show_min_time % 60;
            $show_sec_time = $total % 60;
        

        if( $total > 60 && $total < 3600){
                    echo $show_min_time, _e('minute' , 'write-time'),$show_sec_time,_e('second' , 'write-time');
                } elseif( $total > 3600){
                    echo $show_hor_time,_e('hour' , 'write-time'),$show_min_time,_e('minute' , 'write-time'),$show_sec_time,_e('second' , 'write-time');
                } else{
                    echo $show_sec_time,_e('second' , 'write-time');
                }
        echo '</td>';
        echo '<td>' , edit_post_link('edit') , '</td>';
        echo '</tr>';
        }
        $total_count += $total;
        $count++;
    endwhile;
    echo '</tbody>';
    echo '</table>';
    echo '</div>';

    $show_hor_time = floor($total_count / 3600);
    $show_min_time = floor($total_count / 60);
    $show_min_time = $show_min_time % 60;
    $show_sec_time = $total_count % 60;
    echo '<div class="article_date"';
    echo _e('<p>ALL article' , 'write-time') , $count , '</p>';
    echo _e('<p>ALL write time' , 'write-time') ;
    if( $total_count > 60 && $total_count < 3600){
                echo $show_min_time, _e('minute' , 'write-time'),$show_sec_time,_e('second' , 'write-time');
            } elseif( $total_count > 3600){
                echo $show_hor_time,_e('hour' , 'write-time'),$show_min_time,_e('minute' , 'write-time'),$show_sec_time,_e('second' , 'write-time');
            } else{
                echo $show_sec_time,_e('second' , 'write-time');
            }
    echo '</p>';
    echo '</div>';
    wp_reset_query();
?>

<div id="result"></div>
<script type="text/javascript">
function utc2dateString(utc_msec) {
  d=new Date();
  d.setTime(utc_msec);
  return d.getFullYear()+'/'+(d.getMonth()+1)+'/'+d.getDate();
}

(function($) {

  var url = <?php echo $url ?>;
  var data = <?php echo $json ?>;

  $.post(url,data,function (ret) {
        console.log(ret);
        Highcharts.setOptions({
            global: {
            useUTC: true
        },
        lang: {
            rangeSelectorZoom: '表示範囲',
            resetZoom: '表示期間をリセット',
            resetZoomTitle: '表示期間をリセット',
            rangeSelectorFrom: '表示期間',
            rangeSelectorTo: '〜',
            printButtonTitle: 'チャートを印刷',
            exportButtonTitle: '画像としてダウンロード',
            downloadJPEG: 'JPEG画像でダウンロード',
            downloadPDF: 'PDF文書でダウンロード',
            downloadPNG: 'PNG画像でダウンロード',
            downloadSVG: 'SVG形式でダウンロード',
            months: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
            weekdays: ['日', '月', '火', '水', '木', '金', '土'],
            numericSymbols: null
          }
        });

    

        // Create the chart
        $('#container').highcharts('StockChart', {
            rangeSelector : {
                selected : 1
            },

            title : {
                text : 'write time'
            },

            series : [{
                name : 'write time',
                turboThreshold: 0,
                data : ret,
                tooltip: {
                    shared: true,
                    pointFormat: '{point.y} 分',
                    valueDecimals: 0
                }
            }],

            xAxis: [{
                labels: {
                  formatter: function(){ return utc2dateString(this.value); }
                }
              }],
            navigator: {
                baseSeries: 0
            },

            yAxis: [{
                title: {
                    text: '時間(分)'
                }
            }],

            plotOptions: {
                series: {
                  dataGrouping: {
                    dateTimeLabelFormats: {
                       millisecond: ['%Y/%m/%d %H:%M:%S.%L', '%Y/%m/%d %H:%M:%S.%L', '-%H:%M:%S.%L'],
                       second: ['%Y/%m/%d %H:%M:%S', '%Y/%m/%d %H:%M:%S', '-%H:%M:%S'],
                       minute: ['%Y/%m/%d %H:%M', '%Y/%m/%d %H:%M', '-%H:%M'],
                       hour: ['%Y/%m/%d %H:%M', '%Y/%m/%d %H:%M', '-%H:%M'],
                       day: ['%Y/%m/%d', '%Y/%m/%d', '-%Y/%m/%d'],
                       week: ['%Y/%m/%d', '%Y/%m/%d', '-%Y/%m/%d'],
                       month: ['%B %Y', '%B', '-%B %Y'],
                       year: ['%Y', '%Y', '-%Y']
                    }
                  }
                },
            },
            rangeSelector: {
                selected : 1,
                inputDateFormat: '%Y/%m/%d',
                inputEditDateFormat: '%Y/%m/%d',
                buttons : [{
                    type : 'day',
                    count : 90,
                    text : '3ヶ月'
                }, {
                    type : 'day',
                    count : 180,
                    text : '6ヶ月'
                }, {
                    type : 'year',
                    count : 1,
                    text : '1年'
                    }, {
                    type : 'year',
                    count : 2,
                    text : '2年'
                }, {
                    type : 'year',
                    count : 3,
                    text : '3年'
                }, {
                    type : 'all',
                    count : 1,
                    text : 'All'
                }]
            }
        }, function (chart) {
        // apply the date pickers
        setTimeout(function () {
            $('input.highcharts-range-selector', $(chart.container).parent()).datepicker();}, 0);
            });
        
        // Set the datepicker's date format
        $.datepicker.setDefaults({
            dateFormat: 'yy-mm-dd',
            onSelect: function () {
                this.onchange();
                this.onblur();
            }
        });

    });
})(jQuery);
</script>

<?php
}

function myplugin_response() {
    $nonce_name = myplugin_get_nonce_name();
    check_ajax_referer( $nonce_name, 'security' );

    $ret = array();
    $args = array(
        'meta_key' => 'created_total_time',
        'order' => 'asc',
        'posts_per_page' => -1
        );
    $posts = get_posts( $args );
    foreach( $posts as $key => $post ) {
        $time_stomp = get_post_meta($post->ID , 'created_total_time' ,true);
        $time_stomp = intval($time_stomp);
        $show_hor_time = floor($time_stomp / 3600);
        $show_min_time = floor($time_stomp / 60);

        $data_time = strtotime($post->post_date) * 1000;

        $ret[$key] = array(
            $data_time,$show_min_time
            );
    }
    header( 'Content-Type: application/json' );
    wp_send_json( $ret );
}


?>