<?php

namespace Bowling;

class FrameFactory
{
    public function createFrame(): Frame
    {
        return new Frame();
    }
}
