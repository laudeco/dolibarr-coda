<?php


namespace CodaImporter\Application\Coda\Query;


use CodaImporter\Application\Coda\ViewModel\Statements;
use Codelicious\Coda\Parser;
use Codelicious\Coda\ParserInterface;

final class CodaFileParserQueryHandler
{

    /**
     * @var ParserInterface
     */
    private $parser;

    public function __construct(ParserInterface $parser = null)
    {
        $this->parser = $parser ?: $this->getParser();
    }

    public function query(string $fileName){
        if(empty($fileName)){
            throw new \InvalidArgumentException('The file name couldn\'t be empty');
        }

        $statements = $this->parser->parseFile($fileName);

        return Statements::fromStatements($statements);
    }

    /**
     * @return Parser
     */
    protected function getParser()
    {
        return new Parser();
    }
}