<?php

namespace Tests\Unit\Services;

use App\Services\Imap\BankDataExtractor;
use PHPUnit\Framework\TestCase;

class BankDataExtractorTest extends TestCase
{
    protected BankDataExtractor $extractor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->extractor = new BankDataExtractor();
    }

    public function test_extracts_invoice_keyword_romanian()
    {
        $result = $this->extractor->extract('Factura nr. 123 pentru suma de 500 RON');
        $this->assertTrue($result['is_invoice']);
    }

    public function test_extracts_invoice_keyword_english()
    {
        $result = $this->extractor->extract('Invoice #INV-001 - amount 750 EUR');
        $this->assertTrue($result['is_invoice']);
    }

    public function test_extracts_invoice_keyword_spanish()
    {
        $result = $this->extractor->extract('Factura 2024-001 - cuenta 500');
        $this->assertTrue($result['is_invoice']);
    }

    public function test_non_invoice_returns_false()
    {
        $result = $this->extractor->extract('Just a regular email message');
        $this->assertFalse($result['is_invoice']);
    }

    public function test_extracts_amount_simple()
    {
        $result = $this->extractor->extract('Suma: 500 RON');
        $this->assertEquals(500.0, $result['amount']);
    }

    public function test_extracts_amount_with_decimal_comma()
    {
        $result = $this->extractor->extract('Suma: 1.500,50 RON');
        $this->assertEquals(1500.5, $result['amount']);
    }

    public function test_extracts_amount_with_decimal_dot()
    {
        $result = $this->extractor->extract('Amount: 1,234.56 USD');
        $this->assertEquals(1234.56, $result['amount']);
    }

    public function test_extracts_balance()
    {
        $result = $this->extractor->extract('Sold disponibil: 5.000,00 RON');
        $this->assertEquals(5000.0, $result['balance']);
    }

    public function test_extracts_currency_ron()
    {
        $result = $this->extractor->extract('Suma: 500 RON');
        $this->assertEquals('RON', $result['currency']);
    }

    public function test_extracts_currency_eur()
    {
        $result = $this->extractor->extract('Amount: 100 EUR');
        $this->assertEquals('EUR', $result['currency']);
    }

    public function test_detects_debit_direction()
    {
        $result = $this->extractor->extract('Debit transaction of 500 RON');
        $this->assertEquals('debit', $result['direction']);
    }

    public function test_detects_credit_direction()
    {
        $result = $this->extractor->extract('Credit received: 1000 EUR');
        $this->assertEquals('credit', $result['direction']);
    }

    public function test_full_extraction_romanian()
    {
        $text = 'Factura nr. F-001 - Suma: 1.500,50 RON - Sold: 5.000,00 RON - Debit';
        $result = $this->extractor->extract($text);

        $this->assertTrue($result['is_invoice']);
        $this->assertEquals(1500.5, $result['amount']);
        $this->assertEquals(5000.0, $result['balance']);
        $this->assertEquals('RON', $result['currency']);
        $this->assertEquals('debit', $result['direction']);
    }

    public function test_extraction_with_html()
    {
        $text = '<p>Invoice Amount: <strong>250 EUR</strong></p>';
        $result = $this->extractor->extract($text);

        $this->assertEquals(250.0, $result['amount']);
        $this->assertEquals('EUR', $result['currency']);
    }

    public function test_null_returns_for_missing_data()
    {
        $result = $this->extractor->extract('Some random text');

        $this->assertNull($result['amount']);
        $this->assertNull($result['balance']);
        $this->assertNull($result['currency']);
        $this->assertNull($result['direction']);
    }
}
