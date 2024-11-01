=== write-time ===
Contributors: shinji yonetsu
Donate link: http://11neko.com/
Tags: count,time,content,write
Requires at least: 3.3
Tested up to: 4.1
Stable tag: trunk
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

To display the time it took to write the article

== Description ==

If you write an article by introducing this plug -in , article writing time is recorded .
In addition , you can view the time it took to write the article .

Settings

1. Enable plug -ins
2. write an article
3. Write the code to the part you want to display

`<p><?php display_time('default'); ?></p>`

for Japanese users

導入方法

1. プラグインを有効化する
2. 記事を書く
3. 記事を書いた後に以下のコードを表示させたい場所に記載する

過去記事を編集した場合でも、編集時間が記録されます。
記事投稿時や編集中にサーバーとの接続が切れた場合等に正常に執筆時間が記録されない場合があります。

正常でない時間が表示されていた場合(時間が多い場合)は、管理画面のwrite timeのメニューを開くと自動更新されます。
時間の表示が少なすぎる場合は、次のアップデートで改善予定です。



`<p><?php display_time('default'); ?></p>`
`<p><?php display_time('custom'); ?></p>`

== Installation ==

1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Place `<?php display_time('default'); ?>` in your templates



== Frequently asked questions ==

= A question that someone might have =


An answer to that question.

== Screenshots ==

1. Example that was displayed

== Changelog ==

= 0.3.6 =
add graph　and bugfix.

グラフ表示機能の追加
プラグインメニューを開いた際に、異常な記録時間を修正するようにしました。

= 0.2.2 =
add menu. and bugfix.

= 0.1 =
First release.

== Upgrade notice ==

= 0.1 =
First release.

== Arbitrary section 1 ==