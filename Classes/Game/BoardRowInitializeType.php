<?php

namespace Classes\Game;

enum BoardRowInitializeType
{
    case FIRST_BLACK;
    case SECOND_BLACK;
    case EMPTY;
    case FIRST_WHITE;
    case SECOND_WHITE;
}
