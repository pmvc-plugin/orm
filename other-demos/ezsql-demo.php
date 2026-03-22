<?php
    public function testinit()
    {
      \PMVC\d($this->dsn()->getSupportEngine());
      $sql = $this->ezsql(); 
      $o = $sql->pdoInstance(['sqlite:' . TEST_SQLITE_DB, '', '', [], true]);
      $o->connect();
      $sql->setInstance($o);
      // $o->query("CREATE TABLE test_table2 ( MyColumnA INTEGER PRIMARY KEY, MyColumnB TEXT(32) );");

      var_dump($sql->column('name', TEXT));

      $s = $this->ezquery()->create('profile', $sql->column('id', INTR, 11, AUTO, PRIMARY), $sql->column('name', TEXT));
      var_dump($s);

      $res = $o->get_results("SELECT sql FROM sqlite_master WHERE type='table';");
      var_dump($res);
    }
