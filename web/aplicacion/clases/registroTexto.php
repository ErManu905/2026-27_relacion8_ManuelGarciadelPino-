<?php
class RegistroTexto
{
    private $text;
    private $dateTime;

    // Crear registro de texto
    public function __construct(string $text)
    {
        $this->text = $text;
        $this->dateTime = new DateTime();
    }

    // Obtener texto
    public function getText(): string {return $this->text;}

    // Obtener fecha y hora
    public function getDateTime(): DateTime {return $this->dateTime;}
}
?>