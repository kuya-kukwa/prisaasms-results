"use client";
import CountUp from "react-countup";
import { useInView } from "react-intersection-observer";

interface ScrollCounterProps {
  end: number;
  duration?: number;
  className?: string;
  suffix?: string;
  prefix?: string;
}

export default function ScrollCounter({
  end,
  duration = 3,
  className = "text-4xl font-bold",
  suffix = "",
  prefix = ""
}: ScrollCounterProps) {
  const { ref, inView } = useInView({ triggerOnce: true });

  return (
    <div ref={ref} className={className}>
      {inView && (
        <CountUp
          end={end}
          duration={duration}
          suffix={suffix}
          prefix={prefix}
        />
      )}
    </div>
  );
}