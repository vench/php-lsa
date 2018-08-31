<?php


require '../vendor/autoload.php';

use PHPUnit\Framework\TestCase;

/**
 * Class TestPersistentFile
 */
class TestPersistentFile extends TestCase
{

    /**
     * @return \PHPLsa\PersistentFile
     */
    public function testSave() {
        $dir = dirname(__FILE__) . '/../data';
        $pFile = new \PHPLsa\PersistentFile($dir);
        $pFile->save('test', [1,2,3]);
        $this->assertTrue(file_exists($dir . '/test'));
        return $pFile;
    }

    /**
     * @param \PHPLsa\PersistentFile $pFile
     * @depends testSave
     * @return \PHPLsa\PersistentFile
     */
    public function testLoad(\PHPLsa\PersistentFile $pFile) {
        $data = $pFile->load('test');
        $this->assertTrue(count($data) == 3);
        $this->assertArraySubset($data, [1,2,3]);
        return $pFile;
    }

    /**
     * @param \PHPLsa\PersistentFile $pFile
     * @return \PHPLsa\PersistentFile
     * @depends testLoad
     */
    public function testLSASave(\PHPLsa\PersistentFile $pFile) {

        $documents = [
            'В список таких мест попал Эрмитаж – в подвалах музея сейчас живут 50-60 кошек и котов.  Также любителям кошек рекомендуется побывать в доме-музее Хэмингуэя во Флориде, где живут 57 котов, в Музее кошки в Амстердаме и в литовском Музее кошек в Шяуляе – там собрано около 10 тысяч экспонатов со всего мира: скульптуры, фотографии, витражи с изображением кошек.',
            'Имена для кошек подбираются хозяевами тщательно и обдуманно. Некоторые размышляют над именем еще до появления в доме пушистого комочка. Как назвать кота, приходит в голову и при первой встрече с новым членом семьи. Кошачьи имена подбирают исходя из характера, внешнего вида и даже гастрономических предпочтений питомцев.',
            'Так, самыми популярными породами кошек у россиян стали шотландские и британские, средняя цена которых составила 2,8 тысячи и 3,2 тысячи рублей соответственно. В пятерку также вошли мейн-куны (8,5 тысячи рублей), бенгальские...',
            'В Салавате живёт мастерица, которая шьёт мягкие игрушки по технологии Тедди. Они были особенно популярны в 19-20 веках, но и сейчас их любят и дети, и взрослые. Какие секреты хранит старый плюшевый медведь, расскажет Инна Горина.',
            'Красивую благотворительную акцию устроили болельщики голландского футбольного клуба «Фейеноорд» во время матча чемпионата Нидерландов по футболу против «Эксельсиора». При счете 1:0 зрители верхнего яруса забросали мягкими игрушками сектор с детьми из госпиталя Erasmus Sofia',
        ];

        $lsa = new \PHPLsa\LSA(4);
        $lsa->addTextMatrixTransformer(new \PHPLsa\TfidfText());
        $trans = $lsa->fitTransform($documents);
        $this->assertTrue(!empty($trans));
        $lsa->save($pFile);

        $dir = $pFile->getBaseDirectory();
        $this->assertTrue(file_exists($dir . '/components'));

        return $pFile;

    }

    /**
     * @param \PHPLsa\PersistentFile $pFile
     * @depends testLSASave
     */
    public function testLSALoad(\PHPLsa\PersistentFile $pFile) {

        $lsa = new \PHPLsa\LSA(4);
        $lsa->addTextMatrixTransformer(new \PHPLsa\TfidfText());

        $components = $lsa->getComponents();
        $this->assertTrue(count($components) == 0);

        $lsa->load($pFile);

        $components = $lsa->getComponents();
        $this->assertTrue(count($components) > 0);
    }
}